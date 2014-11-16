<?php
namespace Controller\Page;

/**
 * 駅間の料金を検索するページの作成
 */
class CalculateFareController extends \Controller\ControllerBase
{
    public function route()
    {
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $tran = $this->modules['MsTranslator'];
        $railways = $tmCtrl->getRailWayType();
        $label = array(
            'title' => $tran->translator('運賃'),
            'fromStation' => $tran->translator('出発駅'),
            'toStation' => $tran->translator('到着駅'),
            'calFare' => $tran->translator('運賃を計算'),
            'result' =>  $tran->translator('結果'),
            'ticketFareResult' => $tran->translator('切符利用時の運賃'),
            'childTicketFareResult' => $tran->translator('切符利用時の子供運賃'),
            'icCardFareResult' => $tran->translator('ICカード利用時の運賃'),
            'childIcCardFareResult' => $tran->translator('ICカード利用時の子供運賃')
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
                       'railways' => $railways,
                       'label'=>$label,
                       'gmaplang'=>$gmaplang
        );
        $tempData += $this->getHeaderTempalteData();

        if ($this->isMobile()) {
            $this->app->render('calculate_fare_mobile.tpl', $tempData);
        } else {
            $this->app->render('calculate_fare.tpl', $tempData);
        }
    }
}
