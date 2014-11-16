<?php
namespace Controller\Json;

/**
 * 運賃を計算して返す.<BR>
 */
class CalculateFareController extends \Controller\ControllerBase
{
    public function route()
    {
        $from = $this->app->request->params('from');
        $to = $this->app->request->params('to');
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $stations = array();
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];

        $ret = $tmCtrl->findFareByFromTo($from, $to);
        $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], $ret['contents']);

        return;
    }
}
