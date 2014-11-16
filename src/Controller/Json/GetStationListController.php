<?php
namespace Controller\Json;

/**
 * Jsonとして駅のsameAsとtitleの一覧を返す.<BR>
 * railwayパラメータにて路線による絞込みができるものとする.
 */
class GetStationListController extends \Controller\ControllerBase
{
    public function route()
    {
        $railway = $this->app->request->params('railway');
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $stations = array();
        if (!$railway) {
            $ret = $tmCtrl->getStations();
        } else {
            $ret = $tmCtrl->findStation(
                array(
                    'odpt:railway' => $railway
                )
            );
        }

        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

            return;
        }
        $contents = $ret['contents'];
        foreach ($contents as $c) {
            $station = array('sameAs' => $c->{'owl:sameAs'},
                             'title' => $c->{'dc:title'});
            if ($this->app->request->params('geo')) {
                $station['lat'] = $c->{'geo:lat'};
                $station['long'] = $c->{'geo:long'};
            }
            if ($this->app->request->params('station_code')) {
                $station['stationCode'] = $c->{'odpt:stationCode'};
                $station['stationCodeImage'] = $this->getStationCodeLogoUrl($c->{'odpt:stationCode'});
            }
            array_push($stations, $station);
        }
        $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], $stations);

        return;
    }
}
