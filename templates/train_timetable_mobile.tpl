<!--
 このテンプレートはモバイル版の/{$appName}/train_timetableのレイアウトを記述します.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include_mobile.tpl'}
  <script type="text/javascript" src="/{$appName}/js/train_timetable_mobile.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body {if $rtl}class="rtl"{/if}>
<div id="currentDayType" style="display:none">{$daytype}</div>
<!-- 列車の詳細 -->
{foreach from=$trainTimetables item=info}
  <div id="{$info['timeTableType']}" data-role="page">
    <div data-role="header" data-position="fixed" >
      <h1>{$label['title']}</h1>
      <div data-role="navbar">
        <ul>
          <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
          {foreach from=$trainTimetables item=i}
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
      <h1>{$info['timeTableTypeTitle']}</h1>
      <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
        <thead>
          <tr>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
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
                  <a href="{$info['railwayUrl']}"  rel="external">{$info['railwayTitle']}</a>
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
        </tbody>
      </table>
      <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
        <thead>
          <th scope="col" colspan="2">{$label['station']}</th>
          <th scope="col" >{$label['departureTime']}</th>
          <th scope="col">{$label['note']}</th>
        </thead>
        <tbody>
          {foreach from=$info['timeTable'] item=t}
              <tr>
                  <td>
                      <img src="{$t['stationCodeUrl']}" width="32" height="32" alt="{$t['stationCode']}"/>
                  </td>
                  <td><a href="{$t['stationUrl']}"  rel="external">{$t['stationTitle']}</a></td>
                  <td id="{$info['timeTableType']}_{$t['time']}">{$t['time']}</td>
                  <td>
                    {if $t['womanCarNo']}
                      {$label['womanCar']} : {$t['womanCarNo']}
                    {/if}
                  </td>
              </tr>
          {/foreach}
        </tbosy>
      </table>
      {include file='twitter.tpl' hash=$train_no searchHashTitle=$twitterLabel['searchHashTitle'] alanyzeTermHashTitle=$twitterLabel['alanyzeTermHashTitle']}
    </div>
  </div>
{/foreach}

</body>
</html>
