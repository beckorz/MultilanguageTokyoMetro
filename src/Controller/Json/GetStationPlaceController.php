<?php
namespace Controller\Json;

/**
 * 位置情報から駅の情報を返す.<BR>
 */
class GetStationPlaceController extends \Controller\ControllerBase
{
    public function route()
    {
        $tran = $this->modules['MsTranslator'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $lat = $this->app->request->params('lat');
        $lon = $this->app->request->params('lon');
        $radius = $this->app->request->params('radius');
        $ret = $tmCtrl->findStationByPlace(
            array(
                'lat' => $lat,
                'lon' => $lon,
                'radius' => $radius
            )
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);
            return;
        }
        $railDirectionType = $tmCtrl->getRailDirectionType();
        $railwayType = $tmCtrl->getRailwayType();
        $contents = $ret['contents'];
        $stations = array();
        foreach ($contents as $c) {
            if ($c->{'owl:sameAs'} === 'odpt.Station:TokyoMetro.MarunouchiBranch.NakanoSakaue') {
                continue;
            }
            $station = array('sameAs' => $c->{'owl:sameAs'},
                             'title' => $c->{'dc:title'});
            $station['stationCode'] = $c->{'odpt:stationCode'};
            $station['stationCodeImage'] = $this->getStationCodeLogoUrl($c->{'odpt:stationCode'});
            $station['directions'] = $this->createTimetableLink($c, $railwayType, $railDirectionType);
            array_push($stations, $station);
        }
        $this->sendJsonData(\MyLib\TokyoMetroApi::RESULT_CODE_OK, null, $stations);
    }
}
