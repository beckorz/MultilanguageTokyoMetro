<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include_mobile.tpl'}
  <script type="text/javascript" src="/{$appName}/js/station_timetable_mobile.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body {if $rtl}class="rtl"{/if}>
<div id="currentDayType" style="display:none">{$daytype}</div>
<!-- 列車の詳細 -->
{foreach from=$stationTimetables item=info}
  <div id="{$info['timeTableType']}" data-role="page">
    <div data-role="header" data-position="fixed" >
      <h1>{$label['title']}</h1>
      <div data-role="navbar">
        <ul>
          <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
          {foreach from=$stationTimetables item=i}
            {if $info['timeTableType'] eq $i['timeTableType']}
              <li><a href="#{$i['timeTableType']}"  class="ui-btn-b" data-icon="carat-d" data-transition="slideup">{$i['timeTableTypeTitle']}</a></li>
            {else}
              <li><a href="#{$i['timeTableType']}"  class="ui-btn" data-icon="carat-d" data-transition="slideup">{$i['timeTableTypeTitle']}</a></li>
            {/if}
          {/foreach}

        </ul>
      </div>
    </div>
    <div data-role="main" class="ui-content">
        <h2>
        <img style="vertical-align: middle" width="24" height="24" src="{$stationInfo['stationCodeUrl']}" alt="{$stationInfo['stationCode']}"/>
        <a href="{$stationInfo['railwayUrl']}" rel="external">{$stationInfo['railwayTitle']}</a>
        <a href="{$stationInfo['stationUrl']}" rel="external">{$stationInfo['stationTitle']}</a>
        ({$stationInfo['directionTitle']})
        </h2>
        <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
          <thead>
            <tr>
              <th  scope="col"></th>
              <th  scope="col"></th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$info['timeTable'] key=hour item=min}
              <tr>
                <td>{$hour}</td>
                <td>
                {foreach from=$min item=v}
                  <div style="float:left;padding-right:10px" class="tooltip" 
                    title="
                      <div {if $rtl}class='rtl'{/if}>
                      {$v['departureTime']} <a href='{$v['trainUrl']}#odpt:{$info['timeTableType']}_{$v['departureTime']} ' rel='external'>{$v['trainNumber']}</a> {if $v['trainType']}({$v['trainType']}){/if}<BR>
                      {$label['destinationStation']}:{if $v['destinationStationUrl']}<a href='{$v['destinationStationUrl']}' rel='external'>{$v['destinationStationTitle']}</a>{else}{$v['destinationStationTitle']}{/if}<BR>
                      {$v['note']}
                      </div>
                    ">
                    {$v['min']}
                    {if $v['note']}
                      <img src="/{$appName}/img/note.png" width="10" height="10" alt="note"/>
                    {/if}
                  </div>
                {/foreach}
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
        <div data-role="popup" id="{$info['timeTableType']}_popup" class="ui-content" data-dismissible="false">
          <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
          <span> </span>
        </div>
      </div>
    </div>
  </div>
{/foreach}
<!-- for test-->
<div data-role="page" id="msgPopup">
  <div data-role="header">
    <h1></h1>
  </div>
  <div data-role="main" class="ui-content">
  </div>
</div>
</body>
</html>
