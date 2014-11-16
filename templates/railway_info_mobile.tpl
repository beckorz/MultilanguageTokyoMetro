<!--
 このテンプレートはモバイル用の/{$appName}/raiway_infoのレイアウトを記述します.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include_mobile.tpl'}
  <script type="text/javascript" src="/{$appName}/js/jquery/jsrender.min.js"></script>
  <script type="text/javascript" src="/{$appName}/js/railway_info_base.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript" src="/{$appName}/js/railway_info_mobile.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script id="trainMsgTmpl" type="text/x-jsrender">
    <div {if $rtl}class="rtl"{/if}>
    {literal}
    <a href="/{/literal}{$appName}{literal}/page/train_timetable?train={{:sameAs}}{/literal}&lang={$lang}" rel="external">{literal}{{:trainNumber}}</a>({{:trainType}})<br>
    {/literal}
    {literal}{{:startingStationTitle}}=>{{:terminalStationTitle}}{/literal}<br>
    {$label['delay']} : {literal}{{:delay}}{/literal}<br>
    {$label['createdate']} :{literal}{{:date}}{/literal}<br>
    {$label['valid']} :{literal}{{:valid}}{/literal}<br>
    </div>
  </script>
</head>
<body {if $rtl}class="rtl"{/if}>
  <div id="railway" data-role="page">
    <div data-role="header" data-position="fixed" >
      <h1>{$label['title']}―<img src="{$railwayIcon}"  style="margin-top: -5px;" width="32" height="32" alt=""/> {$railwayTitle}</h1>
      <div data-role="navbar">
        <ul>
          <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
          <li><a id="checkTrain" href="#" class="ui-btn" data-icon="refresh">{$label['updated']}</a></li>
          {if $womenCar}
            <li><a href="#womenCar" class="ui-btn" data-icon="carat-d">{$label['womenCarInfo']}</a></li>
          {/if}
        </ul>
      </div>
    </div>
    <div data-role="main" class="ui-content">
      <select id="railwaySelect">
          {foreach from=$railways key=key item=item}
              <option value="{$key}" img="{$item->icon}">{$item->title}</option>
          {/foreach}
      </select>
      <label for="autoUpdated">{$label['autoUpdated']}:</label>
      <select name="slider" id="autoUpdated" data-role="slider">
        <option value="off">Off</option>
        <option value="on">On</option>
      </select>
      <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
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
                              <a href="{$info['stationUrl']}" rel="external">{$info['title']}</a>
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
                                <a href="{$cnn['url']}" rel="external">{$cnn['title']}</a><br>
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
          </tbody>
      </table>

      <div data-role="popup" id="train_popup" class="ui-content" data-dismissible="false">
        <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
        <span> </span>
      </div>

      {include file='twitter.tpl' hash=$railwaySameAs searchHashTitle=$twitterLabel['searchHashTitle'] alanyzeTermHashTitle=$twitterLabel['alanyzeTermHashTitle']}

    </div>
  </div>
  <!-- 女性専用車両 -->
  <div id="womenCar" data-role="page">
    <div data-role="header" data-position="fixed" >
      <h1>{$label['womenCarInfo']}</h1>
      <div data-role="navbar">
        <ul>
          <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
          <li><a href="#railway" class="ui-btn" data-icon="back">Back</a></li>
        </ul>
      </div>
    </div>
    <div data-role="main" class="ui-content">
      {if $womenCar}
        <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
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
    </div>
  </div>

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
