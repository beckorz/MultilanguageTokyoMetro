<?php
namespace Controller\Page;

/**
 * 現在地からの列車探索ページ
 */
class PosToTrainNoController extends \Controller\ControllerBase
{
    public function route()
    {
        $tran = $this->modules['MsTranslator'];
        $lang = $this->app->request->params('lang');
        $langInfo = $this->modules['JsonCtrl']->getTranslationInfo();
        $gmaplang ='ja';
        if ($lang) {
            if ($langInfo[$lang]) {
                $gmaplang = $langInfo[$lang]->gmapCode;
            }
        }
        $transInfo = $this->modules['JsonCtrl']->getTranslationInfo();
        $label = array(
            'title' => $tran->translator('現在地からの列車検索'),
            'getCurPos' => $tran->translator('現在地取得'),
            'searchStation' => $tran->translator('駅検索'),
            'back' => $tran->translator('戻る'),
            'station' => $tran->translator('駅'),
            'direction' => $tran->translator('方面'),
            'getStationTimetable' => $tran->translator('駅時刻表取得'),
            'getTrainInfo' => $tran->translator('列車情報')
        );
        $tempData = array(
                       'appName' => $this->app->getName(),
                       'lang' => $lang,
                       'rtl' => $transInfo[$this->lang]->rtl,
                       'label'=>$label,
                       'gmaplang'=>$gmaplang
        );
        $this->app->render('pos_to_train_no_mobile.tpl', $tempData);
    }
}
