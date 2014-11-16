<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/station_timetable.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body>

{include file='header.tpl'}

<div id="contents" {if $rtl}class="rtl"{/if}>
  <h1>{$label['title']}</h1>
  <h2>
  <img style="vertical-align: middle" width="24" height="24" src="{$stationInfo['stationCodeUrl']}" alt="{$stationInfo['stationCode']}"/>
  <a href="{$stationInfo['railwayUrl']}">{$stationInfo['railwayTitle']}</a> 
  <a href="{$stationInfo['stationUrl']}">{$stationInfo['stationTitle']}</a> 
  ({$stationInfo['directionTitle']})</h2>
  <div id="tabs">
    <ul>
      {foreach from=$stationTimetables item=info}
        <li><a href="#{$info['timeTableType']}">{$info['timeTableTypeTitle']}</a></li>
      {/foreach}
    </ul>
    <!-- 指定日の日付タイプを設定 -->
    <div id="currentDayType" style="display:none">{$daytype}</div>
    {foreach from=$stationTimetables item=info}
      <div id="{$info['timeTableType']}">
      <table class="normal">
        {foreach from=$info['timeTable'] key=hour item=min}
          <tr>
            <td>{$hour}</td>
            <td>
            {foreach from=$min item=v}
              <div style="float:left;padding-right:10px" class="tooltip" 
                title="
                  <div {if $rtl}class='rtl'{/if}>
                  {$v['departureTime']} <a href='{$v['trainUrl']}#odpt:{$info['timeTableType']}_{$v['departureTime']}'>{$v['trainNumber']}</a> {if $v['trainType']}({$v['trainType']}){/if}<BR>
                  {$label['destinationStation']}:{if $v['destinationStationUrl']}<a href='{$v['destinationStationUrl']}'>{$v['destinationStationTitle']}</a>{else}{$v['destinationStationTitle']}{/if}<BR>
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
      </table>
      </div>
    {/foreach}
  </div>
</div>
</body>
</html>
