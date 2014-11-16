<?php
namespace Controller\Page;

/**
 * 列車時刻表ページの作成
 */
class TrainTimetableController extends \Controller\ControllerBase
{
    public function route()
    {
        $train = $this->app->request->params('train');
        if (!$train) {
            $this->app->halt(500, 'train is not defined.');

            return;
        }

        $tran = $this->modules['MsTranslator'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $holidayCtrl = $this->modules['HolidayCtrl'];


        // 駅情報取得
        $ret = $tmCtrl->getStations();
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

            return;
        }
        $stations = $ret['contents'];
        if (!$stations) {
            $this->app->halt(500, 'internal error.');

            return;
        }

        // 列車タイムテーブル
        $ret = $tmCtrl->findTrainTimetable(
            array(
            'odpt:train' => $train)
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->app->halt(500, $ret['errorMsg']);

            return;
        }

        $contents = $ret['contents'];
        $trainTimetables = array();
        $railDirection = $tmCtrl->getRailDirectionType();
        $trainTypes = $tmCtrl->getTrainType();
        $railways = $tmCtrl->getRailwayType();
        foreach ($contents as $c) {
            if ($c->{'odpt:train'} != $train) {
                continue;
            }

            if ($c->{'odpt:weekdays'}) {
                $table = $this->createTimetable(
                    $c,
                    $stations,
                    $railDirection,
                    $trainTypes,
                    $railways,
                    'odpt:weekdays',
                    '平日'
                );
                array_push($trainTimetables, $table);
            }
            if ($c->{'odpt:saturdays'}) {
                $table = $this->createTimetable(
                    $c,
                    $stations,
                    $railDirection,
                    $trainTypes,
                    $railways,
                    'odpt:saturdays',
                    '土曜日'
                );
                array_push($trainTimetables, $table);
            }
            if ($c->{'odpt:holidays'}) {
                $table = $this->createTimetable(
                    $c,
                    $stations,
                    $railDirection,
                    $trainTypes,
                    $railways,
                    'odpt:holidays',
                    '休日'
                );
                array_push($trainTimetables, $table);
            }
        }
        $daytype = $holidayCtrl->checkDayType(time());
        $railways = $tmCtrl->getRailWayType();
        $label = array(
            'title' => $tran->translator('列車時刻表'),
            'trainNo' => $tran->translator('列車番号'),
            'railway' => $tran->translator('路線'),
            'trainType' => $tran->translator('列車タイプ'),
            'startingStationTitle' => $tran->translator('始発駅'),
            'terminalStationTitle' => $tran->translator('終着駅'),
            'departureTime' => $tran->translator('時刻'),
            'trainOwner' => $tran->translator('所属会社'),
            'operator' => $tran->translator('運行会社'),
            'station' => $tran->translator('駅'),
            'note' => $tran->translator('備考'),
            'womanCar' => $tran->translator('女性専用車両')
        );
        $train_no = $this->getName($train);
        $tempData = array(
            'appName' => $this->app->getName(),
            'label' => $label,
            'daytype'=> $daytype,
            'train_no'=> $train_no,
            'trainTimetables' => $trainTimetables
        );

        $tempData += $this->getHeaderTempalteData();

        if ($this->isMobile()) {
            $this->app->render('train_timetable_mobile.tpl', $tempData);
        } else {
            $this->app->render('train_timetable.tpl', $tempData);
        }
    }

    private function createTimetable($c, $stations, $railDirection, $trainTypes, $railways, $propName, $propTitle)
    {
        $tran = $this->modules['MsTranslator'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $ownerType = $tmCtrl->getTrainOwnerType();
        $operatorType = $tmCtrl->getOperatorType();

        // 路線情報取得
        $womenCar = array();
        $ret = $tmCtrl->findRailway(
            array(
                'owl:sameAs' => $c->{'odpt:railway'}
            )
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->app->halt(500, $ret['errorMsg']);
            return;
        }
        $railwayContent =  $tmCtrl->findContentBySameAs($ret['contents'], $c->{'odpt:railway'});
        if ($railwayContent->{'odpt:womenOnlyCar'}) {
            foreach ($railwayContent->{'odpt:womenOnlyCar'} as $wc) {
                $d = array(
                    'fromStation' => $wc->{'odpt:fromStation'},
                    'toStation' => $wc->{'odpt:toStation'},
                    'order' => $this->compStation($wc->{'odpt:fromStation'},  $wc->{'odpt:toStation'}, $stations),
                    'operationDay' => $wc->{'odpt:operationDay'},
                    'availableTimeFrom' => $wc->{'odpt:availableTimeFrom'},
                    'availableTimeUntil' => $wc->{'odpt:availableTimeUntil'},
                    'carNumber' => $wc->{'odpt:carNumber'}
                );
                array_push($womenCar, $d);
            }
        }
        $info = array(
            'trainNumber' => $c->{'odpt:trainNumber'},
            'railway' => $c->{'odpt:railway'},
            'railwayTitle' => $railways[$c->{'odpt:railway'}]->title,
            'railwayUrl'=> $this->createPageUrl('railway_info', 'railway=' . $c->{'odpt:railway'}),
            'railDirection' => $railDirectio[$c->{'odpt:railDirection'}],
            'trainType' => $trainTypes[$c->{'odpt:trainType'}],
            'trainOwner' => $c->{'odpt:trainOwner'},
            'trainOwnerTitle'=> $ownerType[$c->{'odpt:trainOwner'}],
            'operator' => $c->{'odpt:operator'},
            'operatorTitle'=> $operatorType[$c->{'odpt:operator'}],
            'startingStation'=> $c->{'odpt:startingStation'},
            'startingStationTitle' => $tmCtrl->findStationTitle($stations, $c->{'odpt:startingStation'}),
            'terminalStation'=> $c->{'odpt:terminalStation'},
            'terminalStationTitle' => $tmCtrl->findStationTitle($stations, $c->{'odpt:terminalStation'}),
            'timeTableType' => ''
        );
        $info['timeTableType'] = $propName;
        $info['timeTableTypeTitle'] = $tran->translator($propTitle);
        $info['timeTable'] = array();
        $i = 0;
        $order = $this->compStation($c->{$propName}[0]->{'odpt:departureStation'},  $c->{$propName}[1]->{'odpt:departureStation'}, $stations);
        foreach ($c->{$propName} as $t) {
            $time = $t->{'odpt:departureTime'};
            $sid = $t->{'odpt:departureStation'};
            if (!$sid) {
                $time = $t->{'odpt:arrivalTime'};
                $sid = $t->{'odpt:arrivalStation'};
            }

            $s = $tmCtrl->findContentBySameAs($stations, $sid);
            $womanCarNo = $this->getWomenCarNumber($sid, $time, $propName, $order, $womenCar, $stations);
            $row = array('time'=>$time,
                         'stationCode' => $s->{'odpt:stationCode'},
                         'stationCodeUrl'=> $this->getStationCodeLogoUrl($s->{'odpt:stationCode'}),
                         'stationSameAs'=>$t->{'odpt:departureStation'},
                         'stationTitle'=>$s->{'dc:title'},
                         'stationUrl'=> $this->createPageUrl('station_info', 'station=' . $sid),
                         'womanCarNo' => $womanCarNo
            );
            if ($i == 0) {
                // 他社線始発の場合のみしか格納されていないので、
                // 一番最初のデータを入れとく
                if (!$info['startingStation']) {
                    $info['startingStation'] = $row['stationSameAs'];
                    $info['startingStationTitle'] = $row['stationTitle'];
                }
            }
            array_push($info['timeTable'], $row);
            $i = $i + 1;
        }

        return $info;
    }

    /**
     * fromの駅コード方が大きければ + ... from: M10 to:M05
     * fromの駅コード方が小さければ - ... from: M05 to:M10
     */
    function compStation($from, $to, $stations) {
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $fromContents =  $tmCtrl->findContentBySameAs($stations, $from);
        if (!$fromContents) {
          return 0;
        }
        $toContetns =  $tmCtrl->findContentBySameAs($stations, $to);
        if (!$toContetns) {
          return 0;
        }
        return strcasecmp($fromContents->{'odpt:stationCode'}, $toContetns->{'odpt:stationCode'});
    }

    function getWomenCarNumber($sid, $time, $dayType, $order, $womenCar, $stations) {
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        foreach ($womenCar as $w) {
            if ($dayType != 'odpt:weekdays' || $w['operationDay'] != 'Weekday') {
                continue;
            }
            if (($order == 0 && $order != $w['order']) ||
                ($order > 0 && $w['order'] <= 0) ||
                ($order < 0 && $w['order'] >= 0) ) {
                continue;
            }
            if ($order > 0) {
                // 降順の場合
                if ($this->compStation($sid, $w['fromStation'], $stations) > 0) {
                    // $sidの方が女性開始駅より大きい場合は範囲外
                    continue;
                }

                if ($this->compStation($sid, $w['toStation'], $stations) < 0) {
                    // $sidの方が女性終了駅より小さい場合は範囲外
                    continue;
                }
            } else {
                // 昇順の場合
                if ($this->compStation($sid, $w['fromStation'], $stations) < 0) {
                    // $sidの方が女性開始駅より小さい場合は範囲外
                    continue;
                }

                if ($this->compStation($sid, $w['toStation'], $stations) > 0) {
                    // $sidの方が女性終了駅より大きい場合は範囲外
                    continue;
                }
            }
            if ($tmCtrl->compTime($time, $w['availableTimeFrom']) < 0) {
                // $sidの方が女性開始時間より小さい場合は範囲外
                continue;
            }
            if ($tmCtrl->compTime($time, $w['availableTimeUntil']) > 0) {
                // $sidの方が女性開始時間より大きい場合は範囲外
                continue;
            }
            return $w['carNumber'];
        }
        return null;
    }
}
