<?php
namespace Controller\Page;

/**
 * 翻訳を修正するページ
 */
class TranslationController extends \Controller\ControllerBase
{
    public function route()
    {
        $tempData = array(
            'appName' => $this->app->getName()
        );

        $tempData += $this->getHeaderTempalteData();
        $this->app->render('translation.tpl', $tempData);
    }
}
