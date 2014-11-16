<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include_mobile.tpl'}
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&language={$gmaplang}"></script>
  <script type="text/javascript" src="/{$appName}/js/station_info_base.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript" src="/{$appName}/js/station_info_mobile.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body {if $rtl}class="rtl"{/if}>

<!-- 時刻表へのリンク-->
<div id="stationTimetable" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1><img src="{$stationInfo['stationCodeUrl']}" width="32" height="32" alt="{$stationInfo['stationCode']}"/>{$stationInfo['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
        <li><a href="#stationTimetable" class="ui-btn-b" data-icon="carat-d">{$label['timetable']}</a></li>
        <li><a href="#exit" class="ui-btn" data-icon="carat-d">{$label['exits']}</a></li>
        <li><a href="#barrierfree" class="ui-btn" data-icon="carat-d">{$label['barrierfree']}</a></li>
        <li><a href="#platformInformation" class="ui-btn" data-icon="carat-d">{$label['platformInformation']}</a></li>
      </ul>
    </div>
  </div>

  <div data-role="main" class="ui-content">
    <h2>{$label['timetable']}</h2>
    <a href="{$stationInfo['railwayUrl']}" rel="external">{$stationInfo['railwayTitle']}</a>
    <ul>
      {foreach from=$timetable item=t}
      <li><a href="{$t['directionUrl']}"  rel="external" >{$t['directionTitle']}</a></li>
      {/foreach}
    </ul>
    <h2>{$label['passenger']}</h2>
    <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
      <thead>
        <tr>
          <th scope="col"></th>
          <th scope="col"></th>
       </tr>
      </thead>
      <tbody>
        {foreach from=$passenger item=p}
          <tr>
            <td>{$p['surveyYear']}</td>
            <td class="number">{$p['passengerJourneys']}</td>
          </tr>
        {/foreach}
      </tbody>
    </table>
    {include file='twitter.tpl' hash=$stationName searchHashTitle=$twitterLabel['searchHashTitle'] alanyzeTermHashTitle=$twitterLabel['alanyzeTermHashTitle'] alanyzePosTitle=$twitterLabel['alanyzePosTitle'] lat=$stationInfo['lat'] long=$stationInfo['long'] posLabel=$stationInfo['title'] }


  </div>
</div>

<!-- 出口ページ-->
<div id="exit" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1><img src="{$stationInfo['stationCodeUrl']}" width="32" height="32" alt="{$stationInfo['stationCode']}"/>{$stationInfo['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
        <li><a href="#stationTimetable" class="ui-btn" data-icon="carat-d">{$label['timetable']}</a></li>
        <li><a href="#exit" class="ui-btn-b" data-icon="carat-d">{$label['exits']}</a></li>
        <li><a href="#barrierfree" class="ui-btn" data-icon="carat-d">{$label['barrierfree']}</a></li>
        <li><a href="#platformInformation" class="ui-btn" data-icon="carat-d">{$label['platformInformation']}</a></li>

      </ul>

    </div>
  </div>
  <div data-role="main" class="ui-content">
    <h2>{$label['exits']}</h2>
    <select id="selectExit">
      <option/>
      {foreach from=$exits item=e}
        <option value="{$e['title']}" lat={$e['lat']} long={$e['long']}>{$e['title']}</option>
      {/foreach}
    </select>
    <div id="stationLoc" style="display:none" lat="{$stationInfo['lat']}" long="{$stationInfo['long']}"> </div>
    <div id="map_canvas" style="width: 100%; height: 250px"  class="ui-shadow"></div>
    <div id="pano" style="width: 100%; height: 250px;dispaly=none"  ></div>
  </div>
</div>

<!-- バリアフリーページ-->
<div id="barrierfree" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1><img src="{$stationInfo['stationCodeUrl']}" width="32" height="32" alt="{$stationInfo['stationCode']}"/>{$stationInfo['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
        <li><a href="#stationTimetable" class="ui-btn" data-icon="carat-d">{$label['timetable']}</a></li>
        <li><a href="#exit" class="ui-btn" data-icon="carat-d">{$label['exits']}</a></li>
        <li><a href="#barrierfree" class="ui-btn-b" data-icon="carat-d">{$label['barrierfree']}</a></li>
        <li><a href="#platformInformation" class="ui-btn" data-icon="carat-d">{$label['platformInformation']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <h2>{$label['barrierfree']}</h2>
    <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
       <thead>
         <tr>
           <th scope="col"></th>
           <th scope="col"></th>
           <th scope="col"></th>
           <th scope="col"></th>
         </tr>
       </thead>
       <tbody>
         {foreach from=$barrierfreies item=b}
           <tr id="{$b['sameAs']}">
             <td>
               {if $b['typeIcon']}
                 <img src="{$b['typeIcon']}" alt=""/>
               {/if}
               {$b['categoryName']}
             </td>
             <td>
               {$b['placeName']}
             </td>
             <td>
               {$b['locatedAreaName']}
             </td>
             <td>
               {foreach from=$b['detail'] item=d}
                 {if $d['serviceStartTime'] || $d['serviceEndTime']}
                   <p>{$d['serviceStartTime']}～{$d['serviceEndTime']}</p>
                 {/if}
                 {if $d['operationDay']}
                   <p>{$d['operationDay']}</p>
                 {/if}
                 {if $d['direction']}
                   <p>{$d['direction']}</p>
                 {/if}
               {/foreach}
               {foreach from=$b['hasAssistant'] item=a}
                 <img src="{$a->icon}" class="tooltip" title="{$a->title}" alt=""/>
               {/foreach}
               {if $b['isAvailableTo']}
                 <p>{$b['isAvailableTo']->title}</p>
               {/if}
               {$b['remark']}
             </td>
           </tr>
         {/foreach}
       </tbody>

       <div data-role="popup" id="hasAssistant_popup" class="ui-content" data-dismissible="false">
         <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
         <span> </span>
       </div>

    </table>
  </div>
</div>

<!-- プラットフォームページ -->
<div id="platformInformation" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1><img src="{$stationInfo['stationCodeUrl']}" width="32" height="32" alt="{$stationInfo['stationCode']}"/>{$stationInfo['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
        <li><a href="#stationTimetable" class="ui-btn" data-icon="carat-d">{$label['timetable']}</a></li>
        <li><a href="#exit" class="ui-btn" data-icon="carat-d">{$label['exits']}</a></li>
        <li><a href="#barrierfree" class="ui-btn" data-icon="carat-d">{$label['barrierfree']}</a></li>
        <li><a href="#platformInformation" class="ui-btn-b" data-icon="carat-d">{$label['platformInformation']}</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
    <h2>{$label['platformInformation']}</h2>
    {foreach from=$platforms item=rail}
      <h3>{$rail[0]['railwayTitle']}</h3>
      <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
        <thead>
          <tr>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
         </tr>
        </thead>
        <tbody>
        {foreach from=$rail item=p}
          <tr>
            <th scope="row">{$label['direction']}</th>
            <td>{$p['railDirectionTitle']}</td>
            <th scope="row">{$label['carNumber']}</th>
            <td>{$p['carNumber']}/{$p['carComposition']}</td>
          </tr>
          <tr>
            <td>{$label['transferInformation']}</td>
            <td colspan="3">
              {if $p['transferInformation']}
                <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
                  <thead>
                    <th scope="col">{$label['railway']}</th>
                    <th scope="col">{$label['direction']}</th>
                    <th scope="col">{$label['necessaryTime']}</th>
                  </thead>
                   <tbody>                 
                    {foreach from=$p['transferInformation'] item=t}
                      <tr>
                        <td>
                          {if $t['url']}
                            <a href="{$t['url']}" rel="external">{$t['railwayTitle']}</a>
                          {else}
                            {$t['railwayTitle']}
                          {/if}
                        </td>
                        <td>
                          {$t['railDirectionTitle']}
                        </td>
                        <td class="number">
                          {$t['necessaryTime']}
                        </td>
                      </tr>
                    {/foreach}
                  </tbody>
                </table>
              {/if}
            </td>
          </tr>
          <tr>
            <td>{$label['platformBarrierfree']}</td>
            <td colspan="3">
              {foreach from=$p['barrierfreeFacility'] item=b}
                <p><a href="#" data-page="#barrierfree" data-anchor="#{$b['sameAs']}">{$b['title']}</a></p>
              {/foreach}
            </td>
          </tr>
          <tr>
            <td>{$label['surroundingArea']}</td>
            <td colspan="3">
              {foreach from=$p['surroundingArea'] item=s}
                <p>{$s}</p>
              {/foreach}
            </td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    {/foreach}
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