<?php
namespace Controller\Page;

/**
 * スタートページ
 * アプリケーションと、各機能の解説。
 */
class StartController extends \Controller\ControllerBase
{
    public function route()
    {
        $tran = $this->modules['MsTranslator'];
        $introductText = '';
        $introductText .= "このアプリケーションは東京メトロが提供する様々な情報を多言語対応したものです。\n";
        $introductText .= "駅、路線、時刻表の情報や運行情報などをMicrosoft Translatorによって機械翻訳を行っています。\n";
        $introductText .= "機械翻訳を利用することで、あなたは、いくつかの言語で運行情報や列車位置情報などのリアルタイム情報を閲覧することができます。\n";
        $introductText .= "しかしながら、機械翻訳のため、表示されるメッセージは、適切なメッセージではないかもしれません。\n";
        $introductText .= "その場合は、ツイッターのアカウントでログインすることにより、その不適切なメッセージを自由に修正することができます。\n";
        $introductText .= "\n";
        $introductText .= "なお、このアプリケーションは非公式なもので東京地下鉄株式会社と一切の関係はありません。\n";
        $introductText .= "また、様々な要因によってサービスが提供できなくなる可能性がありますのでご了承ください。\n";

        $featureText = "次の機能を、様々な言語で使用することができます。\n";

        $railwayInfoText = "路線情報を表示します。\n";
        $railwayInfoText .= "各駅間の所要時間や駅情報へのリンク、現在の列車位置情報を表示します。\n";
        $railwayInfoText .= "列車番号にマウスオーバーを行うことで、列車情報の詳細と、列車時刻表へのリンクを表示します。\n";

        $stationInfoText = "駅の詳細情報を表示します。\n駅時刻表へのリンク、駅出入り口情報、施設情報、乗降人員数が確認できます。\nこの画面は路線情報、列車時刻表から表示することができます。\n";

        $stationTimetableText = "駅時刻表では指定の駅において、列車が何時出発するかを確認します。\n出発時刻をマウスオーバーすることにより、列車の情報が表示されます。\nこの画面は駅情報画面から表示することができます。\n";

        $trainTimetableText = "列車時刻表では、指定の列車が駅から何時出発し、何時到着するかを表示します。\nこの画面は駅時刻表、路線情報などから表示することができます。\n";

        $positionText_0 = "位置情報から、最寄駅を取得し、その駅から出発する列車の情報を検索します。";
        $positionText_1 = "1. 位置情報を有効にして、現在値と距離から駅を検索します。";
        $positionText_2 = "2. 最寄の駅の候補から、対象の駅と、方向を指定します。\n";
        $positionText_3 = "3. 現在時刻以降に指定の駅から出発する列車を選択します。\n";
        $positionText_4 = "4. 列車時刻表により、指定の列車の詳細情報を確認できます。\n";

        $fareText = "東京メトロ管理下の駅を二つ選択して、運賃を計算します。\n";

        $historyText = "過去に発生した様々な履歴を確認できます。";

        $trainInfoText = "過去に発生した各路線の運行情報の履歴を確認できます。";

        $trainLocText = "過去７日間の５分周期で取得した列車位置情報の履歴を一覧表の形で確認できます。";

        $trainLocMapText = "過去７日間の５分周期で取得した列車位置情報の履歴を路線図上で確認できます。";
        $trainLocMapText_1 = "1.取得日を指定することで、その当時の列車位置情報を確認できます。";
        $trainLocMapText_2 = "2.列車マークをマウスオーバー、タップを行うことで詳細情報が表示されます。\n「追跡」ボタンをチェックすると、列車マークが点滅しはじめます。";

        $translationLogText = "過去に変更されたテキストの履歴を確認できます。\n";
        $translationLogText .= "作成者、言語、翻訳元のテキストで検索が可能です。\n";
        

        $translationText = "Twitterのアカウントでログインをして、テキストの修正を行います。";

        $twitterAnalyzeText = "路線情報、駅情報、列車時刻表においてTwitterと連携をします。\n";
        $twitterAnalyzeText .= "路線、駅、列車を表すハッシュタグを使用したツイートの投稿や、ハッシュタグの検索、ツイート内容の分析を行います。";
        $twitterAnalyzeText .= "駅情報については、位置情報でツイートの検索し分析することが可能です。\n";
        $twitterAnalyzeText .= "ツイートの解析を行うには、ログインを行う必要があります。";

        $label = array(
            'title' => $tran->translator('地下鉄多言語化MOD'),

            'introduct' => $tran->translator('はじめに'),
            'introductText' => nl2br($tran->translator($introductText)),

            'feature' => $tran->translator('機能'),
            'featureText' => nl2br($tran->translator($featureText)),

            'railwayInfo' => $tran->translator('路線情報'),
            'railwayInfoText' => nl2br($tran->translator($railwayInfoText)),

            'stationInfo' => $tran->translator('駅情報'),
            'stationInfoText' => nl2br($tran->translator($stationInfoText)),

            'stationTimetable' => $tran->translator('駅時刻表'),
            'stationTimetableText' => nl2br($tran->translator($stationTimetableText)),

            'trainTimetable' => $tran->translator('列車時刻表'),
            'trainTimetableText' => nl2br($tran->translator($trainTimetableText)),


            'position' => $tran->translator('現在位置から列車検索'),
            'positionText_0' => nl2br($tran->translator($positionText_0)),
            'positionText_1' => nl2br($tran->translator($positionText_1)),
            'positionText_2' => nl2br($tran->translator($positionText_2)),
            'positionText_3' => nl2br($tran->translator($positionText_3)),
            'positionText_4' => nl2br($tran->translator($positionText_4)),

            'fare' => $tran->translator('運賃の計算'),
            'fareText' => $tran->translator($fareText),

            'history' => $tran->translator('履歴の確認'),
            'historyText' => $tran->translator($historyText),

            'latestTrainInfo' => $tran->translator('最新の運行情報'),
            'trainInfo' => $tran->translator('運行情報の履歴'),
            'trainInfoText' => $tran->translator($trainInfoText),

            'trainLoc' => $tran->translator('列車位置情報履歴(一覧)'),
            'trainLocText' => $tran->translator($trainLocText),

            'trainLocMap' => $tran->translator('列車位置情報履歴(マップ)'),
            'trainLocMapText' => $tran->translator($trainLocMapText),
            'trainLocMapText_1' => nl2br($tran->translator($trainLocMapText_1)),
            'trainLocMapText_2' => nl2br($tran->translator($trainLocMapText_2)),

            'translationLog' => $tran->translator('テキスト変更履歴'),
            'translationLogText' => nl2br($tran->translator($translationLogText)),

            'translation' => $tran->translator('テキスト修正'),
            'translationText' => $tran->translator($translationText),

            'twitterAnalyze' => $tran->translator('Twitterとの連携'),
            'twitterAnalyzeText' => nl2br($tran->translator($twitterAnalyzeText))
        );
        $tempData = array(
            'appName' => $this->app->getName(),
            'label'=>$label
        );
        $tempData += $this->getHeaderTempalteData();
        if ($this->isMobile()) {
            $this->app->render('start_mobile.tpl', $tempData);
        } else {
            $this->app->render('start.tpl', $tempData);
        }
    }
}
