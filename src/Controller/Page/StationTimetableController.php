<?php
namespace Controller\Page;

/**
 * 駅時刻表ページ
 */
class StationTimetableController extends \Controller\ControllerBase
{
    public function route()
    {
        $station = $this->app->request->params('station');
        if (!$station) {
            $this->app->halt(500, 'station is not defined.');

            return;
        }
        $direction = $this->app->request->params('direction');
        if (!$direction) {
            $this->app->halt(500, 'direction is not defined.');

            return;
        }

        $tran = $this->modules['MsTranslator'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $holidayCtrl = $this->modules['HolidayCtrl'];

        $railDirection = $tmCtrl->getRailDirectionType();
        $trainTypes = $tmCtrl->getTrainType();
        $railways = $tmCtrl->getRailwayType();

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
        $ret = $tmCtrl->findStationTimetable(
            array(
                'odpt:station' => $station,
                'odpt:railDirection'=>$direction
            )
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->app->halt(500, $ret['errorMsg']);

            return;
        }
        $stationTimetable = null;
        foreach ($ret['contents'] as $s) {
          if ($s->{'odpt:station'}==$station) {
            $stationTimetable = $s;
            break;
          }
        }
        if (!$stationTimetable) {
            $this->app->halt(500, 'not found station.');
            return;
        }
        $stationTimetables = array();
        if ($stationTimetable->{'odpt:weekdays'}) {
            array_push(
                $stationTimetables,
                array(
                    'timeTableType'=>'weekdays',
                    'timeTableTypeTitle'=>$tran->translator('平日'),
                    'timeTable' => $this->createTimetable($stationTimetable->{'odpt:weekdays'}, $stations, $trainTypes)
                )
            );
        }
        if ($stationTimetable->{'odpt:saturdays'}) {
            array_push(
                $stationTimetables,
                array(
                    'timeTableType'=>'saturdays',
                    'timeTableTypeTitle'=>$tran->translator('土曜'),
                    'timeTable' => $this->createTimetable($stationTimetable->{'odpt:saturdays'}, $stations, $trainTypes)
                )
            );
        }
        if ($stationTimetable->{'odpt:holidays'}) {
            array_push(
                $stationTimetables,
                array(
                    'timeTableType'=>'holidays',
                    'timeTableTypeTitle'=>$tran->translator('休日'),
                    'timeTable' => $this->createTimetable($stationTimetable->{'odpt:holidays'}, $stations, $trainTypes)
                )
            );
        }
        $s = $tmCtrl->findContentBySameAs($stations, $station);
        $stationInfo = array(
            'sameAs' => $station,
            'stationUrl' => $this->createPageUrl('station_info', 'station=' . $station),
            'stationTitle'=> $s->{'dc:title'},
            'stationCode'=> $s->{'odpt:stationCode'},
            'stationCodeUrl'=> $this->getStationCodeLogoUrl($s->{'odpt:stationCode'}),
            'railway' => $s->{'odpt:railway'},
            'railwayTitle' => $railways[$s->{'odpt:railway'}]->title,
            'railwayUrl' => $this->createPageUrl('railway_info', 'railway=' . $s->{'odpt:railway'}),
            'direction' => $direction,
            'directionTitle' => $railDirection[$direction]
        );
        $daytype = $holidayCtrl->checkDayType(time());
        $label = array(
            'title' => $tran->translator('駅時刻表'),
            'destinationStation' => $tran->translator('行き先駅')
        );
        $tempData = array(
            'appName' => $this->app->getName(),
            'label' => $label,
            'daytype'=> $daytype,
            'stationInfo' => $stationInfo,
            'stationTimetables' => $stationTimetables
        );
        $tempData += $this->getHeaderTempalteData();
        if ($this->isMobile()) {
            $this->app->render('station_timetable_mobile.tpl', $tempData);
        } else {
            $this->app->render('station_timetable.tpl', $tempData);
        }
    }

    private function createTimetable($table, $stations, $trainTypes)
    {
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $hours=array();
        foreach ($table as $row) {
            $ds = $tmCtrl->findContentBySameAs($stations, $row->{'odpt:destinationStation'});
            $dt = $tmCtrl->findStationTitle($stations, $row->{'odpt:destinationStation'});
            $durl = null;
            if ($ds) {
                $durl= $this->createPageUrl('station_info', 'station=' . $ds->{'owl:sameAs'});
            }
            $dtime=explode(':', $row->{'odpt:departureTime'});
            if (!$hours[$dtime[0]]) {
                $hours+= array($dtime[0]=>array());
            }
            $trainType = NULL;
            if ($row->{'odpt:trainType'} != 'odpt.TrainType:TokyoMetro.Local') {
                $trainType = $trainTypes[$row->{'odpt:trainType'}];
            }
            $data =array(
                'min'=> $dtime[1],
                'departureTime' => $row->{'odpt:departureTime'},
                'trainType' => $trainType,
                'destinationStation' => $row->{'odpt:destinationStation'},
                'destinationStationTitle' => $dt,
                'destinationStationUrl' => $durl,
                'trainNumber' => $row->{'odpt:trainNumber'},
                'train' => $row->{'odpt:train'},
                'trainUrl' => $this->createPageUrl('train_timetable', 'train=' . $row->{'odpt:train'}),
                'carComposition' => $row->{'odpt:carComposition'},
                'note' => $row->{'odpt:note'},
                'isOrigin' => $row->{'odpt:isOrigin'},
                'isLast' => $row->{'odpt:isLast'}
            );
            array_push($hours[$dtime[0]], $data);
        }

        return $hours;
    }
}
