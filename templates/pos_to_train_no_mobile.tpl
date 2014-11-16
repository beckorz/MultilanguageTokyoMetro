<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include_mobile.tpl'}
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&language={$gmaplang}"></script>
  <script type="text/javascript" src="/{$appName}/js/pos_to_train_no_mobile.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body {if $rtl}class="rtl"{/if}>

<!-- 現在地を選択するためのページ -->
<div id="mapPage" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1>{$label['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
        <li><a id="btnSearchStation" href="#" class="ui-btn" data-icon="search">{$label['searchStation']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <label for="flipGetCurPos">{$label['getCurPos']}:</label>
    <select name="slider" id="flipGetCurPos" data-role="slider">
      <option value="off">Off</option>
      <option value="on">On</option>
    </select>
    <div id="map_canvas" style="width: 100%; height: 250px"  class="ui-shadow"></div>
    <select name="radius" id="selectRadius">
      <option value="100">100m</option>
      <option value="300">300m</option>
      <option value="500">500m</option>
      <option value="1000">1km</option>
    </select>
  </div>
</div>

<div data-role="page" id="msgPopup">
  <div data-role="header">
    <h1></h1>
  </div>
  <div data-role="main" class="ui-content">
  </div>
</div>
<!-- 駅と方向を選択するためのページ -->
<div id="stationPage" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1>{$label['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a id="backToMapPage" href="#mapPage" class="ui-btn" data-icon="arrow-l">{$label['back']}</a></li>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
        <li><a id="btnSearchStationTimeTable" href="#" class="ui-btn" data-icon="search">{$label['getStationTimetable']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <label for="selectStation">{$label['station']}:</label>
    <select name="selectStation" id="selectStation" ></select>
    <label for="selectDirection">{$label['direction']}:</label>
    <select name="selectDirection" id="selectDirection">
    </select>
  </div>
</div>

<!-- 列車を選択するためのページ -->
<div id="stationTimetablePage" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1>{$label['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a id="backToStationPage" href="#stationPage" class="ui-btn" data-icon="arrow-l">{$label['back']}</a></li>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
        <li><a id="btnSearchTrain" rel="external" href="#" class="ui-btn" data-icon="search">{$label['getTrainInfo']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <select name="selectTrain" id="selectTrain">
    </select>
  </div>
</div>
</body>
</html>