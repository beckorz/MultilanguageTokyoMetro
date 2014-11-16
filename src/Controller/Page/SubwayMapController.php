<?php
namespace Controller\Page;

/**
 * 地下鉄マップに過去の位置情報をプロットする画面を作成する
 */
class SubwayMapController extends \Controller\ControllerBase
{
    public function route()
    {
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $model = $this->models['TrainLogModel'];
        $tran = $this->modules['MsTranslator'];
        $railwayInfo = $tmCtrl->getRailWayType();
        $stations = array();
        $ret = $tmCtrl->getStations();
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            echo $ret['errorMsg'];

            return;
        }
        $contents = $ret['contents'];
        foreach ($contents as $c) {
            $idName = self::getName($c->{'owl:sameAs'});
            if (!isset($stations[$idName])) {
                $stations += array(
                    $idName=>array(
                        'title'=> $c->{'dc:title'},
                        'lat'=> $c->{'geo:lat'},
                        'long'=> $c->{'geo:long'},
                        'marker'=>'station',
                        'connect' => $c->{'odpt:connectingRailway'}
                    )
                );
            } else {
                $stations[$idName]['connect'] = array_merge(
                    $stations[$idName]['connect'],
                    $c->{'odpt:connectingRailway'}
                );
                $stations[$idName]['connect'] = array_unique($stations[$idName]['connect']);
                $stations[$idName]['marker'] = 'interchange';
            }
        }
        $ret = $tmCtrl->getRailways();
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            echo $ret['errorMsg'];

            return;
        }
        $railways = array();
        $railwayContents = $ret['contents'];
        $drawstationCount = array();
        foreach ($railwayContents as $c) {
            $stationOrder = array();
            $cnt = 0;
            foreach ($c->{'odpt:stationOrder'} as $s) {
                $idName = self::getName($s->{'odpt:station'});
                $station = $stations[$idName];
                $marker = $station['marker'];
                if ($cnt == 0 || $cnt == count($c->{'odpt:stationOrder'}) -1 ) {
                    $marker = 'interchange';
                }
                array_push(
                    $stationOrder,
                    array(
                        'idName' => $idName,
                        'title'=> $station['title'],
                        'lat'=> $station['lat'],
                        'long'=> $station['long'],
                        'marker'=>  $marker,
                        'x' => self::convertLongToX($station['long']),
                        'y' => self::convertLatToY($station['lat'])
                    )
                );
                $cnt += 1;
            }
            $railway = array('sameAs'=> $c->{'owl:sameAs'},
                             'title' => $c->{'dc:title'},
                             'color' => $railwayInfo[$c->{'owl:sameAs'}]->color,
                             'stationOrder' => $stationOrder);
            array_push($railways, $railway);
        }

        $selectdates = array();
        $ret = $model->getUpdated(0, time());
        foreach ($ret as $key => $row) {
            array_push($selectdates, date("Y/m/d H:i:s", $ret[$key]['updated']));
        }
        $label = array(
          'title' => $tran->translator('列車位置情報の履歴(マップ)'),
          'created'=> $tran->translator('作成日時'),
          'valid'=> $tran->translator('有効期限'),
          'TrainNo'=> 'Train No.',
          'trainType'=> $tran->translator('列車タイプ'),
          'Railway'=> $tran->translator('路線名'),
          'direction'=> $tran->translator('方面'),
          'TrainNo'=> 'Train No.',
          'from'=> 'from',
          'to'=> 'to',
          'StatingStation'=> $tran->translator('始発駅'),
          'TerminalStation'=> $tran->translator('終着駅'),
          'delay'=> $tran->translator('遅延(秒)'),
          'trace'=> $tran->translator('追跡'),
          'dataDate'=> $tran->translator('取得日')
        );
        $tempData = array(
            'appName' => $this->app->getName(),
            'label' => $label,
            'railways' => $railways,
            'selectdates'=> $selectdates
        );
        $tempData += $this->getHeaderTempalteData();
        $this->app->render('subway_map.tpl', $tempData);
    }

    public static function convertLongToX($long)
    {
        return (($long-139.602140)*200);
    }
    public static function convertLatToY($lat)
    {
        return ($lat-35.797316)*-200;
    }
}
