<?php
namespace Controller\Page;

/**
 * 駅詳細情報を表示するページ
 */
class StationInfoController extends \Controller\ControllerBase
{
    public function __construct($app, $modules, $models)
    {
        parent::__construct($app, $modules, $models);
    }

    public function route()
    {
        $station = $this->app->request->params('station');
        if (!$station) {
            $station = 'odpt.Station:TokyoMetro.Marunouchi.Ikebukuro';
        }
        $tran = $this->modules['MsTranslator'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];

        $railwayType = $tmCtrl->getRailwayType();
        $facilityType = $tmCtrl->getFacilityType();
        $railDirectionType = $tmCtrl->getRailDirectionType();
        $otherRailwayType = $tmCtrl->getOtherRailWayType();
        $ret = $tmCtrl->findStation(array('owl:sameAs'=>$station));
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->app->halt(500, $ret['errorMsg']);

            return;
        }
        $stations = $ret['contents'];
        $target = $tmCtrl->findContentBySameAs($stations, $station);
        $ret = $tmCtrl->findStationFacility(
            array('owl:sameAs'=>$target->{'odpt:facility'})
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->app->halt(500, $ret['errorMsg']);

            return;
        }
        $facility = $tmCtrl->findContentBySameAs($ret['contents'], $target->{'odpt:facility'});

        // バリアフリー情報の作成
        $barrierfreies = array();
        foreach ($facility->{'odpt:barrierfreeFacility'} as $b) {
            if (!$b) {
                continue;
            }
            $data = array(
                'sameAs' => $b->{'owl:sameAs'},
                'type' => $b->{'@type'},
                'categoryName' => $b->{'ugsrv:categoryName'},
                'typeIcon' => $facilityType[$b->{'@type'}]->icon,
                'placeName' => $b->{'odpt:placeName'},
                'locatedAreaName' => $b->{'odpt:locatedAreaName'},
                'remark'=> $b->{'ugsrv:remark'},
                'hasAssistant'=> array(),
                'isAvailableTo'=> $facilityType[$b->{'spac:isAvailableTo'}],
                'detail' => array()
            );
            if ($b->{'spac:hasAssistant'}) {
                foreach ($b->{'spac:hasAssistant'} as $item) {
                    array_push($data['hasAssistant'], $facilityType[$item]);
                }
            }
            if ($b->{'odpt:serviceDetail'}) {
                foreach ($b->{'odpt:serviceDetail'} as $d) {
                    $dt = array('serviceStartTime'=> $d->{'ugsrv:serviceStartTime'},
                                'serviceEndTime'=> $d->{'ugsrv:serviceEndTime'},
                                'operationDay'=> $d->{'odpt:operationDay'},
                                'direction'=> $d->{'ug:direction'});
                    array_push($data['detail'], $dt);
                }
            }
            $barrierfreies += array($data['sameAs']=>$data);
        }

        // プラットフォーム情報の作成
        $platforms = array();
        foreach ($facility->{'odpt:platformInformation'} as $p) {
            if ($target->{'odpt:railway'} != $p->{'odpt:railway'}) {
                continue;
            }
            $r = $railwayType[$p->{'odpt:railway'}];
            $data = array(
                'railway' => $p->{'odpt:railway'},
                'railwayTitle'=> $r->title,
                'carComposition'=>$p->{'odpt:carComposition'},
                'carNumber'=>$p->{'odpt:carNumber'},
                'railDirection'=>$p->{'odpt:railDirection'},
                'railDirectionTitle'=>$railDirectionType[$p->{'odpt:railDirection'}],
                'transferInformation'=>array(),
                'barrierfreeFacility'=>array(),
                'surroundingArea'=>array()
            );
            if ($p->{'odpt:barrierfreeFacility'}) {
                foreach ($p->{'odpt:barrierfreeFacility'} as $b) {
                    $bInfo = $barrierfreies[$b];
                    $dt = array('sameAs' => $b,
                                'title' => $bInfo['categoryName'] . ' ' . $bInfo['placeName']);
                    array_push($data['barrierfreeFacility'], $dt);
                }
            }
            if ($p->{'odpt:transferInformation'}) {
                foreach ($p->{'odpt:transferInformation'} as $t) {
                    $tr = $railwayType[$t->{'odpt:railway'}];
                    $url = null;
                    if ($tr) {
                        $url = $this->createPageUrl('railway_info', 'railway=' . $t->{'odpt:railway'});
                    } else {
                        $tr = $otherRailwayType[$t->{'odpt:railway'}];
                        $url = $tr->url;
                    }
                    $dt = array('railway'=>$t->{'odpt:railway'},
                                'railwayTitle'=>$tr->title,
                                'railDirection'=>$t->{'odpt:railDirection'},
                                'railDirectionTitle'=>$railDirectionType[$t->{'odpt:railDirection'}],
                                'url' => $url,
                                'necessaryTime'=>$t->{'odpt:necessaryTime'});
                    array_push($data['transferInformation'], $dt);
                }
            }
            if (!$data['transferInformation'] &&
                !$data['barrierfreeFacility'] &&
                !$data['surroundingArea']) {
                continue;
            }
            if ($p->{'odpt:surroundingArea'}) {
                foreach ($p->{'odpt:surroundingArea'} as $s) {
                    array_push($data['surroundingArea'], $s);
                }
            }
            if (!$platforms[$data['railway']]) {
                $platforms += array($data['railway']=>array());
            }
            array_push($platforms[$data['railway']], $data);
        }

        // 出口情報の作成
        $exits = array();
        foreach ($target->{'odpt:exit'} as $e) {
            $ret = $tmCtrl->findPoi(array('@id'=>$e));
            if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->app->halt(500, $ret['errorMsg']);

                return;
            }
            $exitContents = $ret['contents'];
            foreach ($exitContents as $ec) {
                $data = array(
                    'title' => $ec->{'dc:title'},
                    'categoryName' => $ec->{'ugsrv:categoryName'},
                    'floor' => $ec->{'ug:floor'},
                    'long' => $ec->{'geo:long'},
                    'lat' => $ec->{'geo:lat'}
                );
                array_push($exits, $data);
            }
        }

        // 時刻表へのリンクを作成
        $timetable = $this->createTimetableLink($target, $railwayType, $railDirectionType);

        // 駅乗降人員数
        $passenger = array();
        foreach ($target->{'odpt:passengerSurvey'} as $pass) {
            $ret = $tmCtrl->findPassengerSurvey(array('owl:sameAs' => $pass));
            if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->app->halt(500, $ret['errorMsg']);
                return;
            }
            array_push(
                $passenger,
                array(
                    'surveyYear' => $ret['contents'][0]->{'odpt:surveyYear'},
                    'passengerJourneys' => $ret['contents'][0]->{'odpt:passengerJourneys'}
                )
            );
        }
        $stationName = $this->getName($station);

        // 駅情報の作成
        $stationInfo = array(
          'sameAs' => $target->{'owl:sameAs'},
          'title' => $target->{'dc:title'},
          'railway' => $target->{'odpt:railway'},
          'railwayTitle' => $railwayType[$target->{'odpt:railway'}]->title,
          'railwayUrl' => $this->createPageUrl('railway_info', 'railway=' . $target->{'odpt:railway'}),
          'stationCode' => $target->{'odpt:stationCode'},
          'stationCodeUrl'=> $this->getStationCodeLogoUrl($target->{'odpt:stationCode'}),
          'long' => $target->{'geo:long'},
          'lat' => $target->{'geo:lat'}
        );

        $label = array(
            'title' => $tran->translator('駅情報'),
            'barrierfree'=> $tran->translator('バリアフリー設備'),
            'platformInformation'=> $tran->translator('プラットフォーム情報'),
            'direction'=>$tran->translator('方面'),
            'carNumber'=>$tran->translator('号車番号'),
            'necessaryTime'=>$tran->translator('所要時間(分)'),
            'railway'=>$tran->translator('路線'),
            'transferInformation'=>$tran->translator('最寄りの乗り換え可能な路線'),
            'platformBarrierfree'=>$tran->translator('最寄りのバリアフリー施設'),
            'surroundingArea'=>$tran->translator('改札外の最寄り施設'),
            'passenger'=>$tran->translator('駅乗降人員数'),
            'timetable'=>$tran->translator('時刻表'),
            'exits'=>$tran->translator('駅出入口情報')
        );

        $lang = $this->app->request->params('lang');
        $langInfo = $this->modules['JsonCtrl']->getTranslationInfo();
        $gmaplang ='ja';
        if ($lang) {
            if ($langInfo[$lang]) {
                $gmaplang = $langInfo[$lang]->gmapCode;
            }
        }
        $tempData = array(
            'appName' => $this->app->getName(),
            'label' => $label,
            'stationName' => $stationName,
            'barrierfreies' => $barrierfreies,
            'platforms' => $platforms,
            'exits' => $exits,
            'timetable'=>$timetable,
            'passenger'=>$passenger,
            'stationInfo'=> $stationInfo,
            'gmaplang'  => $gmaplang
        );
        $tempData += $this->getHeaderTempalteData();

        if ($this->isMobile()) {
            $this->app->render('station_info_mobile.tpl', $tempData);
        } else {
            $this->app->render('station_info.tpl', $tempData);
        }
    }
}
