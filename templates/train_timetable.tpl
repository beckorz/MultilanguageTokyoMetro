<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="/{$appName}/css/themes/cupertino/jquery-ui.min.css" type="text/css" />
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/train_timetable.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body>
{include file='header.tpl'}

<div id="contents" {if $rtl}class="rtl"{/if}>
  <h1>{$label['title']}</h1>
  <div id="tabs">
    <ul>
      {foreach from=$trainTimetables item=info}
        <li><a href="#{$info['timeTableType']}">{$info['timeTableTypeTitle']}</a></li>
      {/foreach}
    </ul>
    <!-- 指定日の日付タイプを設定 -->
    <div id="currentDayType" style="display:none">{$daytype}</div>
    {foreach from=$trainTimetables item=info}
      <div id="{$info['timeTableType']}">
        <table class="normal">
            <tr>
                <th scope="row">
                    {$label['trainNo']}
                </th>
                <td>
                    {$info['trainNumber']}
                </td>
            </tr>
            <tr>
                <th scope="row">
                    {$label['trainOwner']}
                </th>
                <td>
                    {$info['trainOwnerTitle']}
                </td>
            </tr>
            <tr>
                <th scope="row">
                    {$label['operator']}
                </th>
                <td>
                    {$info['operatorTitle']}
                </td>
            </tr>
            <tr>
                <th scope="row">
                    {$label['railway']}
                </th>
                <td>
                    <a href="{$info['railwayUrl']}">{$info['railwayTitle']}</a>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    {$label['trainType']}
                </th>
                <td>
                    {$info['trainType']}
                </td>
            </tr>
            <tr>
                <th scope="row">
                    {$label['startingStationTitle']}
                </th>
                <td>
                    {$info['startingStationTitle']}
                </td>
            </tr>
            <tr>
                <th scope="row">
                    {$label['terminalStationTitle']}
                </th>
                <td>
                    {$info['terminalStationTitle']}
                </td>
            </tr>
        </table>
        <BR>
        <table class="normal">
            <tr>
                <th colspan="2" scope="col">{$label['station']}</th>
                <th scope="col">{$label['departureTime']}</th>
                <th scope="col">{$label['note']}</th>
            </tr>
            {foreach from=$info['timeTable'] item=t}
                <tr>
                    <td>
                        <img src="{$t['stationCodeUrl']}" width="32" height="32" alt="{$t['stationCode']}"/>
                    </td>
                    <td><a href="{$t['stationUrl']}">{$t['stationTitle']}</a></td>
                    <td id="{$info['timeTableType']}_{$t['time']}">{$t['time']}</td>
                    <td>
                      {if $t['womanCarNo']}
                        {$label['womanCar']} : {$t['womanCarNo']}
                      {/if}
                    </td>
                </tr>
            {/foreach}
        </table>
      </div>
    {/foreach}
  </div>

  {include file='twitter.tpl' hash=$train_no searchHashTitle=$twitterLabel['searchHashTitle'] alanyzeTermHashTitle=$twitterLabel['alanyzeTermHashTitle']}

</div>
</body>
</html>
