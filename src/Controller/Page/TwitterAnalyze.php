<?php
namespace Controller\Page;


/**
 * スタートページ
 * アプリケーションと、各機能の解説。
 */
class TwitterAnalyze extends \Controller\ControllerBase
{
    public function route()
    {
        $twCtrl = $this->modules['TwitterCtrl'];
        $tran = $this->modules['MsTranslator'];

        $sts = $twCtrl->getStatus();
        if ($sts !== \MyLib\TwitterCtrl::STATUS_AUTHORIZED) {
            $this->app->halt(412, 'permission error.');
            return;
        }

        $title = htmlspecialchars($this->app->request->params('title'));
        $hash = urldecode($this->app->request->params('hash'));
        $hash = preg_replace('/[^A-Za-z0-9 ]/', '', $hash);
        $hash = '#' . htmlspecialchars($hash);

        $posLabel = htmlspecialchars($this->app->request->params('posLabel'));

        $long = urldecode($this->app->request->params('long'));
        $long = preg_replace('/[^0-9.]/', '', $long);
        $long = htmlspecialchars($long);

        $lat = urldecode($this->app->request->params('lat'));
        $lat = preg_replace('/[^0-9.]/', '', $lat);
        $lat = htmlspecialchars($lat);

        if ($posLabel) {
            $txt = $this->modules['Config']['PYTHON_PATH'] . ' ' .
                   dirname(__FILE__) . '/../../../script/analyzeTwitterPlace.py ' .
                   $twCtrl->getConsumerKey() . ' '.
                   $twCtrl->getConsumerSecret() . ' '.
                   $twCtrl->getAccessToken() . ' '.
                   $twCtrl->getAccessSecret() . ' '.
                   '"' . $lat . '" ' .
                   '"' . $long . '" ' .
                   '"0.3km"';
            $subTitle=$posLabel;
        } else {
            $txt = $this->modules['Config']['PYTHON_PATH'] . ' ' .
                   dirname(__FILE__) . '/../../../script/analyzeTwitterTag.py ' .
                   $twCtrl->getConsumerKey() . ' '.
                   $twCtrl->getConsumerSecret() . ' '.
                   $twCtrl->getAccessToken() . ' '.
                   $twCtrl->getAccessSecret() . ' '.
                   '"' . $hash . '"';
            $subTitle=$hash;
        }
        $ret = exec($txt, $output, $retval);

        if ($retval == 0) {
            $ret = implode("\n", $output);
            $tmpData = json_decode($ret);
            $tmpData = array(
              'appName' => $this->app->getName(),
              'title' => $title ,
              'subTitle' => $subTitle,
              'text' => $tran->translator('一週間以内、最大100件のツイートにおいて解析しています。'),
              'data' => $ret
            );
            $this->app->render('twitter_analyze.tpl', $tmpData);
        } elseif ($retval == 254) {
            $this->app->halt(500, 'Internal Error:'. $retval . ':'. implode("<BR>\n", $output));
        } else {
            $this->app->halt(500, 'Internal Error');
        }
    }
}
