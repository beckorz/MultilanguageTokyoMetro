<?php
namespace Controller\Page;

/**
 * 路線情報を表示する画面
 */
class RailwayInfoController extends \Controller\ControllerBase
{
    private $railwayType;
    private $otherRailwayType;
    public function __construct($app, $modules, $models)
    {
        parent::__construct($app, $modules, $models);

        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $this->railwayType = $tmCtrl->getRailWayType();
        $this->otherRailwayType = $tmCtrl->getOtherRailWayType();
    }

    public function route()
    {
        $railway = $this->app->request->params('railway');
        $tran = $this->modules['MsTranslator'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        if (!$railway) {
            $railway = 'odpt.Railway:TokyoMetro.Ginza';
        }
        // 路線情報の生成
        $womenCar = array();
        $railwayInfo = $this->getRailwayInfo($railway, $womenCar);
        // 対象の路線が丸ノ内線分岐の場合は通常の丸の内線と合成する
        if ($railway=='odpt.Railway:TokyoMetro.MarunouchiBranch') {

            $railwayInfoMainLine = $this->getRailwayInfo('odpt.Railway:TokyoMetro.Marunouchi', $womenCar);
            $append_flg = false;
            foreach ($railwayInfoMainLine as $r) {
                if ($r['stationCode'] == 'M06') {
                    // 中野坂上の駅情報はメインラインのURLを使用するようにして、
                    // あとはセルをマージする
                    $railwayInfo[count($railwayInfo)-1]['rowspan'] = 2;
                    $railwayInfo[count($railwayInfo)-1]['rowspanCnn'] = 3;
                    $railwayInfo[count($railwayInfo)-1]['stationUrl'] = $r['stationUrl'];
                    $r['hiddenTitle'] = true;

                    // 中野坂上以降のデータは追加するようにする
                    $append_flg = true;
                }

                if ($append_flg) {
                    array_push($railwayInfo, $r);
                }
            }
        }
        $railwaySameAs = $this->removeSymbol($railway);
        $label = array(
            'title' => $tran->translator('路線情報'),
            'updated' => $tran->translator('更新'),
            'autoUpdated' => $tran->translator('自動更新'),
            'information' => $tran->translator('乗り換え'),
            'station' => $tran->translator('駅'),
            'createdate' => $tran->translator('生成日時'),
            'valid' => $tran->translator('有効期限'),
            'delay'=> $tran->translator('遅延'),
            'womenCarInfo' => $tran->translator('女性専用車両情報'),
            'womenCarFromStation' => $tran->translator('開始駅'),
            'womenCarToStation' => $tran->translator('終了駅'),
            'womenCarOperationDay' => $tran->translator('車両実施曜日'),
            'womenCarAvailableTimeFrom' => $tran->translator('開始時間'),
            'womenCarAvailableTimeUntil' => $tran->translator('終了時間'),
            'womenCarCarComposition' => $tran->translator('車両編成数'),
            'womenCarCarNumber' => $tran->translator('実施車両号車番号')
        );
        $tempData = array(
                       'appName' => $this->app->getName(),
                       'label' => $label,
                       'railwayTitle' => $this->railwayType[$railway]->title,
                       'railwayIcon' => $this->railwayType[$railway]->icon,
                       'railwaySameAs' => $railwaySameAs,
                       'railways' => $this->railwayType,
                       'railwayInfo' => $railwayInfo,
                       'womenCar' => $womenCar
                   );
        $tempData += $this->getHeaderTempalteData();

        if ($this->isMobile()) {
            $this->app->render('railway_info_mobile.tpl', $tempData);
        } else {
            $this->app->render('railway_info.tpl', $tempData);
        }
    }

    /**
     * 路線情報の取得
     * @param  string          $railway 路線ID
     * @return 結果の配列
     */
    private function getRailwayInfo($railway, &$womenCar)
    {
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        // 路線情報
        $ret = $tmCtrl->findRailway(
            array(
                'owl:sameAs' => $railway
            )
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

            return;
        }
        $railwayContents = $ret['contents'];

        // 駅情報
        $ret = $tmCtrl->findStation(
            array(
                'odpt:railway' => $railway
            )
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

            return;
        }

        $stationContents = $ret['contents'];
        $railwayContent =  $tmCtrl->findContentBySameAs($railwayContents, $railway);

        if ($railwayContent->{'odpt:womenOnlyCar'}) {
            foreach ($railwayContent->{'odpt:womenOnlyCar'} as $wc) {
                $d = array(
                    'fromStation' => $wc->{'odpt:fromStation'},
                    'fromStationTitle' => $tmCtrl->findStationTitle($stationContents, $wc->{'odpt:fromStation'}),
                    'toStation' => $wc->{'odpt:toStation'},
                    'toStationTitle' => $tmCtrl->findStationTitle($stationContents, $wc->{'odpt:toStation'}),
                    'operationDay' => $wc->{'odpt:operationDay'},
                    'availableTimeFrom' => $wc->{'odpt:availableTimeFrom'},
                    'availableTimeUntil' => $wc->{'odpt:availableTimeUntil'},
                    'carComposition' => $wc->{'odpt:carComposition'},
                    'carNumber' => $wc->{'odpt:carNumber'}
                );
                array_push($womenCar, $d);
            }
        }

        $betweenStations = array();
        foreach ($railwayContent->{'odpt:travelTime'} as $ttm) {
            $key = $this->createBetweenKey($ttm->{'odpt:fromStation'}, $ttm->{'odpt:toStation'});
            $value = array( 'key' => $this->removeSymbol($key),
                            'necessaryTime'=> $ttm->{'odpt:necessaryTime'});
            $betweenStations += array($key => $value);
        }
        $preStation = null;
        $railwayInfo = array();

        foreach ($railwayContent->{'odpt:stationOrder'} as $order) {
            $s = $tmCtrl->findContentBySameAs($stationContents, $order->{'odpt:station'});
            $connect = array();
            foreach ($s->{'odpt:connectingRailway'} as $c) {
                $connectRail = $this->railwayType[$c];
                $url = '';
                if ($connectRail) {
                    $url = $this->createPageUrl('railway_info', 'railway=' .$c);
                } else {
                    $connectRail = $this->otherRailwayType[$c];
                    $url = $connectRail->url;
                }
                if ($connectRail) {
                    $cinfo = array(
                        'sameAs' => $c,
                        'title' => $connectRail->title,
                        'url' => $url
                    );
                } else {
                    $cinfo = array(
                        'sameAs' => $c,
                        'title' => $c
                    );
                }
                array_push($connect, $cinfo);
            }
            $item = array('type' => 'station',
                          'sameAs' => $s->{'owl:sameAs'},
                          'key' => $this->removeSymbol($s->{'owl:sameAs'}),
                          'title' => $s->{'dc:title'},
                          'stationUrl' => $this->createPageUrl('station_info', 'station=' . $s->{'owl:sameAs'}),
                          'connectingRailway' => $connect,
                          'rowspan' => 1,
                          'rowspanCnn' => 2,
                          'stationCode' => $s->{'odpt:stationCode'},
                          'stationCodeUrl'=> $this->getStationCodeLogoUrl($s->{'odpt:stationCode'}));
            if ($preStation) {
                $curName = $item['sameAs'];
                $keyAtoB = $key = $this->createBetweenKey($preStation, $curName);
                $keyBtoA = $key = $this->createBetweenKey($curName, $preStation);
                $between = array('type' => 'between',
                              'AtoB' => $betweenStations[$keyAtoB],
                              'BtoA' => $betweenStations[$keyBtoA]);
                array_push($railwayInfo, $between);
            }

            array_push($railwayInfo, $item);

            $preStation = $item['sameAs'];
        }
        $railwayInfo[count($railwayInfo)-1]['rowspanCnn'] = 1;

        return $railwayInfo;
    }

    private function createBetweenKey($fromStation, $toStation)
    {
        if ($fromStation == 'odpt.Station:TokyoMetro.MarunouchiBranch.NakanoSakaue') {
            $fromStation = 'odpt.Station:TokyoMetro.Marunouchi.NakanoSakaue';
        }
        if ($toStation == 'odpt.Station:TokyoMetro.MarunouchiBranch.NakanoSakaue') {
            $toStation = 'odpt.Station:TokyoMetro.Marunouchi.NakanoSakaue';
        }

        return $fromStation . '_' . $toStation;
    }
}
