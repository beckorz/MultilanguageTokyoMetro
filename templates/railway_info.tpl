<!--
 このテンプレートはモバイル用の/{$appName}/railway_infoのレイアウトを記述します.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/jquery/jsrender.min.js"></script>
  <script type="text/javascript" src="/{$appName}/js/railway_info_base.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript" src="/{$appName}/js/railway_info.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>

  <script id="trainMsgTmpl" type="text/x-jsrender">
    <div {if $rtl}class="rtl"{/if}>
    {literal}
    <a href="/{/literal}{$appName}{literal}/page/train_timetable?train={{:sameAs}}{/literal}&lang={$lang}">{literal}{{:trainNumber}}</a>({{:trainType}})<br>
    {/literal}
    {literal}{{:startingStationTitle}}=>{{:terminalStationTitle}}{/literal}<br>
    {$label['delay']} : {literal}{{:delay}}{/literal}<br>
    {$label['createdate']} :{literal}{{:date}}{/literal}<br>
    {$label['valid']} :{literal}{{:valid}}{/literal}<br>
    </div>
  </script>
</head>
<body>
{include file='header.tpl'}
<div id="contents" {if $rtl}class="rtl"{/if}>
  <h1>{$label['title']}―<img src="{$railwayIcon}"  style="margin-top: -5px;" width="32" height="32" alt=""/> {$railwayTitle}</h1>
  <select id="railwaySelect">
      {foreach from=$railways key=key item=item}
          <option value="{$key}" img="{$item->icon}">{$item->title}</option>
      {/foreach}
  </select>
  <button id="checkTrain">{$label['updated']}</button>
  <input type="checkbox" id="autoUpdated"/><label for="autoUpdated">{$label['autoUpdated']}</label>

  <table class="normal">
      <tr>
           <th colspan="2">{$label['station']}</th>
           <th>▼</th>
           <th>▲</th>
           <th>{$label['information']}</th>
      </tr>
     {foreach from=$railwayInfo item=info}
          <tr>
              {if $info['type'] eq 'station'}
                  {if !$info['hiddenTitle']}
                    <td rowspan="{$info['rowspan']}">
                        <img src="{$info['stationCodeUrl']}" width="32" height="32" alt="{$info['stationCode']}"/>
                    </td>
                    <td rowspan="{$info['rowspan']}">
                        <a href="{$info['stationUrl']}">{$info['title']}</a>
                    </td>
                  {/if}
                  <td>
                      <div id="{$info['key']}Dn" class="train_location"/>
                  </td>
                  <td>
                      <div id="{$info['key']}Up" class="train_location"/>
                  </td>
                  {if !$info['hiddenTitle']}
                  <td rowspan="{$info['rowspanCnn']}">
                      {foreach from=$info['connectingRailway'] item=cnn}
                        {if $cnn['url'] }
                          <a href="{$cnn['url']}">{$cnn['title']}</a><br>
                        {else}
                          {$cnn['title']}<br>
                        {/if}
                      {/foreach}
                  </td>
                  {/if}
              {/if}
              {if $info['type'] eq 'between'}
                  <td colspan="2" align="center">
                     ↓{$info['AtoB']['necessaryTime']}
                     ↑{$info['BtoA']['necessaryTime']}
                  </td>
                  <td>
                      <div id="{$info['AtoB']['key']}" class="train_location"/>
                  </td>
                  <td>
                      <div id="{$info['BtoA']['key']}" class="train_location"/>
                  </td>
              {/if}
          </tr>
      {/foreach}
  </table>
  {if $womenCar}
  <h2>{$label['womenCarInfo']}</h2>
  <table class="normal">
      <thead>
        <th>{$label['womenCarFromStation']}</th>
        <th>{$label['womenCarToStation']}</th>
        <th>{$label['womenCarOperationDay']}</th>
        <th>{$label['womenCarAvailableTimeFrom']}</th>
        <th>{$label['womenCarAvailableTimeUntil']}</th>
        <th>{$label['womenCarCarComposition']}</th>
        <th>{$label['womenCarCarNumber']}</th>
      </thead>
      <tbody>
        {foreach from=$womenCar item=carInfo}
        <tr>
          <td>{$carInfo['fromStationTitle']}</td>
          <td>{$carInfo['toStationTitle']}</td>
          <td>{$carInfo['operationDay']}</td>
          <td>{$carInfo['availableTimeFrom']}</td>
          <td>{$carInfo['availableTimeUntil']}</td>
          <td>{$carInfo['carComposition']}</td>
          <td>{$carInfo['carNumber']}</td>
        </tr>
        {/foreach}
      </tbody>
  </table>
  {/if}
  {include file='twitter.tpl' hash=$railwaySameAs searchHashTitle=$twitterLabel['searchHashTitle'] alanyzeTermHashTitle=$twitterLabel['alanyzeTermHashTitle']}
</div>
</body>
</html>
