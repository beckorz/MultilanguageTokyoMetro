<?php
namespace Controller\Json;

/**
 * 列車ロケーション情報を取得する
 */
class GetTrainLocationController extends \Controller\ControllerBase
{
    private $railwayDirectionInfo;

    public function __construct($app, $modules, $models)
    {
        parent::__construct($app, $modules, $models);
        $jsonCtrl = $this->modules['JsonCtrl'];
        $this->railwayDirectionInfo = $jsonCtrl->getTrainDirectionConvertTable();
    }

    public function route()
    {
        $railway = $this->app->request->params('railway');
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        if (!$railway) {
            $this->app->halt(400, 'railway parameter is not setted.');

            return;
        }
        if ($railway == 'odpt.Railway:TokyoMetro.MarunouchiBranch') {
            $railway = 'odpt.Railway:TokyoMetro.Marunouchi';
        }

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
        //
        $ret = $tmCtrl->findTrain(
            array(
                'odpt:railway' => $railway
            )
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

            return;
        }
        $trainContents = $ret['contents'];
        $traininfo = array();
        $trainTypes = $tmCtrl->getTrainType();
        $railDirection = $tmCtrl->getRailDirectionType();
        foreach ($trainContents as $c) {
            $location = $c->{'odpt:fromStation'};
            if ($c->{'odpt:toStation'}) {
                $location = $c->{'odpt:fromStation'} . '_' . $c->{'odpt:toStation'};
            } else {
                $location = $location .$this->getDirection($c);
            }
            $location = $this->removeSymbol($location);
            $traintype = $trainTypes[$c->{'odpt:trainType'}];
            $info = array('trainNumber' => $c->{'odpt:trainNumber'},
                          'trainType' => $traintype,
                          'sameAs' => $c->{'owl:sameAs'},
                          'delay' => $c->{'odpt:delay'},
                          'railway' => $c->{'odpt:railway'},
                          'railway' => $c->{'odpt:railway'},
                          'direction' => $c->{'odpt:railDirection'},
                          'directionTitle' => $railDirection[$c->{'odpt:railDirection'}],
                          'startingStation' => $c->{'odpt:startingStation'},
                          'terminalStation' => $c->{'odpt:terminalStation'},
                          'startingStationTitle' => $tmCtrl->findStationTitle($stations, $c->{'odpt:startingStation'}),
                          'terminalStationTitle' => $tmCtrl->findStationTitle($stations, $c->{'odpt:terminalStation'}),
                          'date'=> $c->{'dc:date'},
                          'valid'=> $c->{'dct:valid'},
                          'location' => $location);
            array_push($traininfo, $info);
        }
        $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], $traininfo);
    }

    private function getDirection($s)
    {
        foreach ($this->railwayDirectionInfo[$s->{'odpt:railway'}]->{$s->{'odpt:railDirection'}} as $direct => $cond) {
            if (!$cond) {
                return $direct;
            }
            foreach ($cond as $c) {
                if ($s->{'odpt:fromStation'} == $c) {
                    return $direction;
                }
            }
        }

        return '';
    }
}
