<!--
 このテンプレートはモバイル用の/{$appName}/のレイアウトを記述します.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  {include file='common_include_mobile.tpl'}
  <script type="text/javascript" src="/{$appName}/js/jquery.bxslider/jquery.bxslider.js"></script>
  <script type="text/javascript" src="/{$appName}/js/start_mobile.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body {if $rtl}class="rtl"{/if}>

<!-- 設定ページ-->
<div id="main" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1>{$label['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="#main" class="ui-btn-b" data-icon="carat-d">Main</a></li>
        <li><a href="#introduct" class="ui-btn" data-icon="carat-d">{$label['introduct']}</a></li>
        <li><a href="#feature" class="ui-btn" data-icon="carat-d">{$label['feature']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <p>{$headLabel['contact']}:</span><a href="https://twitter.com/mima_ita" rel="external">mima_ita</a></p>
    <label>{$headLabel['selectLang']}</label>
    <select id="langSelect">
      {foreach from=$langList key=key item=item}
        {if $lang eq $key}
          <option value="{$key}" selected>{$item->title}</option>
        {else}
          <option value="{$key}" >{$item->title}</option>
        {/if}
      {/foreach}
    </select>
    <label>Twitter</label>
    {if $user}
      <a href="/{$appName}/logout?lang={$lang}" class="ui-btn" rel="external">{$headLabel['logout']}</a>
    {else}
       <a href="/{$appName}/login?lang={$lang}" class="ui-btn" rel="external">{$headLabel['login']}</a>
    {/if}
    <label>{$label['latestTrainInfo']}</label>
    <a id="latestTrainInfo" href="#" data-role="button"  data-icon="refresh">UPDATE</a>
    <div id="latestTrainInfoResult"></div>
  </div>
</div>

<div id="introduct" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1>{$label['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="#main" class="ui-btn" data-icon="carat-d">Main</a></li>
        <li><a href="#introduct" class="ui-btn-b" data-icon="carat-d">{$label['introduct']}</a></li>
        <li><a href="#feature" class="ui-btn" data-icon="carat-d">{$label['feature']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <h2>{$label['introduct']}</h2>
    {$label['introductText']}
    <h3>Link</h3>
    <ul>
    <li><a href="https://developer.tokyometroapp.jp/info">東京メトロ オープンデータサイト</a></li>
    <li><a href="https://datamarket.azure.com/dataset/1899a118-d202-492c-aa16-ba21c33c06cb">Microsoft Translator</a></li>
    <li><a href="https://github.com/mima3/MultilanguageTokyoMetro">Github</a></li>
    </ul>
  </div>
</div>

<div id="feature" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1>{$label['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="#main" class="ui-btn" data-icon="carat-d">Main</a></li>
        <li><a href="#introduct" class="ui-btn" data-icon="carat-d">{$label['introduct']}</a></li>
        <li><a href="#feature" class="ui-btn-b" data-icon="carat-d">{$label['feature']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <h2>{$label['feature']}</h2>
    {$label['featureText']}
    <hr>
    <a id="railwayLink" href="/{$appName}/page/railway_info?lang={$lang}" rel="external"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/railway.png" alt="">{$label['railwayInfo']}</img></h3></a>
    {$label['railwayInfoText']}
    <hr>
    <h4>{$label['stationInfo']}</h4>
    {$label['stationInfoText']}
    <hr>
    <h4>{$label['stationTimetable']}</h4>
    {$label['stationTimetableText']}
    <hr>
    <h4>{$label['trainTimetable']}</h4>
    {$label['trainTimetableText']}
    <hr>
    <a href="/{$appName}/page/pos_to_train_no?lang={$lang}" rel="external"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/position.png" alt="">{$label['position']}</img></h3></a>
    {$label['positionText_0']}
    <hr>
    <a href="/{$appName}/page/calculate_fare?lang={$lang}" rel="external"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/money.png" alt="">{$label['fare']}</img></h3></a>
    {$label['fareText']}
    <hr>
    <h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/history.png" alt="">{$label['history']}</img></h3>
    {$label['historyText']}
    <hr>
    <h4><a href="/{$appName}/page/train_info?lang={$lang}" rel="external">{$label['trainInfo']}</a></h4>
    {$label['trainInfoText']}
    <hr>
    <h4><a href="/{$appName}/page/train_location_log?lang={$lang}" rel="external">{$label['trainLoc']}</a></h4>
    {$label['trainLocText']}
    <hr>
    <h4><a href="/{$appName}/page/subway_map?lang={$lang}" rel="external">{$label['trainLocMap']}</a></h4>
    {$label['trainLocMapText']}
    <hr>
    <h4><a href="/{$appName}/page/translation_log?lang={$lang}" rel="external">{$label['translationLog']}</a></h4>
    {$label['translationLogText']}
    <hr>
    <a href="/{$appName}/page/translation?lang={$lang}" rel="external"><h3><img class="btn_icon" style="vertical-align: middle;" src="/{$appName}/img/edit.png" alt="">{$label['translation']}</img></h3></a>
    {$label['translationText']}
    <hr>
    <h3>{$label['twitterAnalyze']}</h3>
    {$label['twitterAnalyzeText']}

  </div>
</div>

<div id="trainInfo" data-role="page">
</div>
</body>
</html>
