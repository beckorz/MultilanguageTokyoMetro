<?php
namespace Controller\Json;

/**
 * Jsonとして路線の形を表すJSONを返す.<BR>
 * railwayパラメータにて路線による絞込みができるものとする.
 */
class GetRailwayRegionController extends \Controller\ControllerBase
{
    public function route()
    {
        $railway = $this->app->request->params('railway');
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $regions = array();
        if (!$railway) {
            $ret = $tmCtrl->getRailways();
        } else {
            $ret = $tmCtrl->findRailway(
                array(
                    'owl:sameAs' => $railway
                )
            );
        }
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

            return;
        }
        $contents = $ret['contents'];
        foreach ($contents as $c) {
            $ret = $tmCtrl->getJson($c->{'ug:region'});
            if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

                return;
            }
            $regions += array($c->{'owl:sameAs'}=>$ret['contents']);
        }

        $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], $regions);

        return;
    }
}
