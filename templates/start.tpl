<!--
 このテンプレートは/{$appName}/のレイアウトを記述します.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <link rel="stylesheet" href="/{$appName}/js/jquery.bxslider/jquery.bxslider.css">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/jquery.bxslider/jquery.bxslider.js"></script>
  <script type="text/javascript" src="/{$appName}/js/start.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body>
{include file='header.tpl'}
<div id="contents" {if $rtl}class="rtl"{/if}>
  <h1>{$label['title']}</h1>
  <h2>{$label['introduct']}</h2>
  {$label['introductText']}
  <h3>Link</h3>
  <ul>
  <li><a href="https://developer.tokyometroapp.jp/info">東京メトロ オープンデータサイト</a></li>
  <li><a href="https://datamarket.azure.com/dataset/1899a118-d202-492c-aa16-ba21c33c06cb">Microsoft Translator</a></li>
  <li><a href="https://github.com/mima3/MultilanguageTokyoMetro">Github</a></li>
  </ul>
  
  <h2>{$label['feature']}</h2>
  {$label['featureText']}

  <a href="/{$appName}/page/railway_info?lang={$lang}"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/railway.png" alt="">{$label['railwayInfo']}</img></h3></a>
  {$label['railwayInfoText']}
  <ul class="bxslider">
    <li><img  src="/{$appName}/img/help/railway_info_001.png" alt="slide:{$label['railwayInfo']} page 1." /></li>
    <li><img  src="/{$appName}/img/help/railway_info_002.png" alt="slide:{$label['railwayInfo']} page 2."/></li>
  </ul>

  <h4>{$label['stationInfo']}</h4>
  {$label['stationInfoText']}
  <ul class="bxslider">
    <li><img  src="/{$appName}/img/help/station_001.png" alt="slide:{$label['stationInfo']} page 1."/></li>
    <li><img  src="/{$appName}/img/help/station_002.png" alt="slide:{$label['stationInfo']} page 2."/></li>
    <li><img  src="/{$appName}/img/help/station_003.png" alt="slide:{$label['stationInfo']} page 3."/></li>
    <li><img  src="/{$appName}/img/help/station_004.png" alt="slide:{$label['stationInfo']} page 4."/></li>
  </ul>

  <h4>{$label['stationTimetable']}</h4>
  {$label['stationTimetableText']}
  <ul class="bxslider">
    <li><img  src="/{$appName}/img/help/station_timetable_001.png" alt="slide:{$label['stationTimetable']} page 1."/></li>
    <li><img  src="/{$appName}/img/help/station_timetable_002.png" alt="slide:{$label['stationTimetable']} page 2."/></li>
  </ul>

  <h4>{$label['trainTimetable']}</h4>
  {$label['trainTimetableText']}
  <ul class="bxslider">
    <li><img  src="/{$appName}/img/help/train_timetable_001.png" alt="slide:{$label['trainTimetable']} page 1."/></li>
  </ul>

  <a href="/{$appName}/page/pos_to_train_no?lang={$lang}"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/position.png" alt="">{$label['position']}</img></h3></a>
  {$label['positionText_0']}
  <ul class="bxslider">
    <li><img src="/{$appName}/img/help/position_001.png" title="{$label['positionText_1']}" alt=""/></li>
    <li><img src="/{$appName}/img/help/position_002.png" title="{$label['positionText_2']}" alt=""/></li>
    <li><img src="/{$appName}/img/help/position_003.png" title="{$label['positionText_3']}" alt=""/></li>
    <li><img src="/{$appName}/img/help/position_004.png" title="{$label['positionText_4']}" alt=""/></li>
  </ul>
  <br>

  <a href="/{$appName}/page/calculate_fare?lang={$lang}"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/money.png" alt="">{$label['fare']}</img></h3></a>
  {$label['fareText']}
  <ul class="bxslider">
    <li><img src="/{$appName}/img/help/fare_001.png" alt="slide:{$label['fare']} page 1." /></li>
  </ul>

  <h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/history.png" alt="">{$label['history']}</img></h3>
  {$label['historyText']}

  <h4><a href="/{$appName}/page/train_info?lang={$lang}">{$label['trainInfo']}</a></h4>
  {$label['trainInfoText']}

  <h4><a href="/{$appName}/page/train_location_log?lang={$lang}">{$label['trainLoc']}</a></h4>
  {$label['trainLocText']}

  <h4><a href="/{$appName}/page/subway_map?lang={$lang}">{$label['trainLocMap']}</a></h4>
  {$label['trainLocMapText']}
  <ul class="bxslider">
    <li><img  src="/{$appName}/img/help/subway_map001.png" title="{$label['trainLocMapText_1']}" alt=""/></li>
    <li><img  src="/{$appName}/img/help/subway_map002.png" title="{$label['trainLocMapText_2']}"  alt="."/></li>
  </ul>

  <h4><a href="/{$appName}/page/translation_log?lang={$lang}">{$label['translationLog']}</a></h4>
  {$label['translationLogText']}

  <a href="/{$appName}/page/translation?lang={$lang}"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/edit.png" alt="">{$label['translation']}</img></h3></a>
  {$label['translationText']}
  <ul class="bxslider">
    <li><img  src="/{$appName}/img/help/translation_001.png" alt="slide:{$label['translation']} page 1."/></li>
    <li><img  src="/{$appName}/img/help/translation_002.png" alt="slide:{$label['translation']} page 2."/></li>
    <li><img  src="/{$appName}/img/help/translation_003.png" alt="slide:{$label['translation']} page 3."/></li>
    <li><img  src="/{$appName}/img/help/translation_004.png" alt="slide:{$label['translation']} page 4."/></li>
    <li><img  src="/{$appName}/img/help/translation_005.png" alt="slide:{$label['translation']} page 5."/></li>
    <li><img  src="/{$appName}/img/help/translation_006.png" alt="slide:{$label['translation']} page 6."/></li>
  </ul>

  <h3>{$label['twitterAnalyze']}</h3>
  {$label['twitterAnalyzeText']}
  <ul class="bxslider">
    <li><img  src="/{$appName}/img/help/twitter_analyze_001.png" alt="slide:{$label['twitterAnalyze']} page 1."/></li>
    <li><img  src="/{$appName}/img/help/twitter_analyze_002.png" alt="slide:{$label['twitterAnalyze']} page 2."/></li>
    <li><img  src="/{$appName}/img/help/twitter_analyze_003.png" alt="slide:{$label['twitterAnalyze']} page 3."/></li>
    <li><img  src="/{$appName}/img/help/twitter_analyze_004.png" alt="slide:{$label['twitterAnalyze']} page 4."/></li>
  </ul>
</div>
</body>
</html>
