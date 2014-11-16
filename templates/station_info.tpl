<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/jquery/jsrender.min.js"></script>
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&language={$gmaplang}"></script>
  <script type="text/javascript" src="/{$appName}/js/station_info_base.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript" src="/{$appName}/js/station_info.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body>

<!-- ヘッダ情報 -->
{include file='header.tpl'}

<div id="contents" {if $rtl}class="rtl"{/if}>
  <h1>
    <img src="{$stationInfo['stationCodeUrl']}" width="32" height="32" alt="{$stationInfo['stationCode']}"/>
    <a href="{$stationInfo['railwayUrl']}">{$stationInfo['railwayTitle']}</a>―{$stationInfo['title']}
  </h1>
  <h2>{$label['timetable']}</h2>
  <ul>
    {foreach from=$timetable item=t}
    <li><a href="{$t['directionUrl']}">{$t['directionTitle']}</a></li>
    {/foreach}
  </ul>  
  <h2>{$label['exits']}</h2>
  <select id="selectExit">
    <option/>
    {foreach from=$exits item=e}
      <option value="{$e['title']}" lat={$e['lat']} long={$e['long']}>{$e['title']}</option>
    {/foreach}
  </select>
  <div id="stationLoc" style="display:none" lat="{$stationInfo['lat']}" long="{$stationInfo['long']}"> </div>
  <table>
    <tr>
      <td>
      <div id="map_canvas" style="width: 400px; height: 300px"></div>
      </td>
      <td>
      <div id="pano" style="width: 400px; height: 300px;"></div>
      </td>
    <tr>
  </table>
  <h2>{$label['barrierfree']}</h2>
  <table class="normal">
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
  </table>
  <h2>{$label['platformInformation']}</h2>
  {foreach from=$platforms item=rail}
    <h3>{$rail[0]['railwayTitle']}</h3>
    <table class="normal">
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
              <table class="normal">
                  <th scope="col">{$label['railway']}</th>
                  <th scope="col">{$label['direction']}</th>
                  <th scope="col">{$label['necessaryTime']}</th>
                  
                  {foreach from=$p['transferInformation'] item=t}
                    <tr>
                      <td>
                        {if $t['url']}
                          <a href="{$t['url']}">{$t['railwayTitle']}</a>
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
              </table>
            {/if}
          </td>
        </tr>
        <tr>
          <td>{$label['platformBarrierfree']}</td>
          <td colspan="3">
            {foreach from=$p['barrierfreeFacility'] item=b}
              <p><a href="#{$b['sameAs']}">{$b['title']}</a></p>
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
    </table>
  {/foreach}
  <h2>{$label['passenger']}</h2>
  <table class="normal">
    {foreach from=$passenger item=p}
       <tr>
         <td>{$p['surveyYear']}</td>
         <td class="number">{$p['passengerJourneys']}</td>
       </tr>
    {/foreach}
  </table>
  {include file='twitter.tpl' hash=$stationName searchHashTitle=$twitterLabel['searchHashTitle'] alanyzeTermHashTitle=$twitterLabel['alanyzeTermHashTitle'] alanyzePosTitle=$twitterLabel['alanyzePosTitle'] lat=$stationInfo['lat'] long=$stationInfo['long'] posLabel=$stationInfo['title'] }

</div>
</body>
</html>
