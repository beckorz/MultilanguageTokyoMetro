<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include_mobile.tpl'}
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&language={$gmaplang}"></script>
  <script type="text/javascript" src="/{$appName}/js/calculate_fare_base.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript" src="/{$appName}/js/calculate_fare_mobile.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body {if $rtl}class="rtl"{/if}>

<!-- 現在地を選択するためのページ -->
<div id="mapPage" data-role="page">
  <div data-role="header" data-position="fixed" >
    <h1>{$label['title']}</h1>
    <div data-role="navbar">
      <ul>
        <li><a href="/{$appName}/?lang={$lang}" rel="external" class="ui-btn" data-icon="home">Home</a></li>
      </ul>
    </div>
  </div>
  <div data-role="main" class="ui-content">
     <div id="map_canvas" style="width: 100%; height: 250px"  class="ui-shadow"></div>
     <lable for="fromRailwaySelect">{$label['fromStation']}</label>
     <select id="fromRailwaySelect" name="fromRailwaySelect">
         <option value="" selected=true></option>
         {foreach from=$railways key=key item=item}
             <option value="{$key}" img="{$item->icon}">{$item->title}</option>
         {/foreach}
     </select>
     <select id="fromStationSelect">
       <option value="">　　　　　　 </option>
     </select>

     <lable for="fromRailwaySelect">{$label['toStation']}</label>
     <select id="toRailwaySelect">
         <option value="" selected=true></option>
         {foreach from=$railways key=key item=item}
             <option value="{$key}" img="{$item->icon}">{$item->title}</option>
         {/foreach}
     </select>
     <select id="toStationSelect">
         <option value="">　　　　　　 </option>
     </select>

     <a id="calculateFareBtn" class="ui-btn">{$label['calFare']}</a>
     <table data-role="table" data-mode="reflow" class="ui-responsive ui-shadow">
       <thead>
         <th col_span="2" scope="col">{$label['result']}</th>
       </thead>
       <tbody>
         <tr>
           <th scope="row"/>
             {$label['ticketFareResult']}
           </th>
           <td>
             <div id="ticketFareResult" class="number">
           </td>
         </tr>
         <tr>
           <th scope="row"/>
             {$label['childTicketFareResult']}
           </th>
           <td>
             <div id="childTicketFareResult" class="number">
           </td>
         </tr>
         <tr>
           <th scope="row"/>
             {$label['icCardFareResult']}
           </th>
           <td>
             <div id="icCardFareResult" class="number">
           </td>
         </tr>
         <tr>
           <th scope="row"/>
             {$label['childIcCardFareResult']}
           </th>
           <td>
             <div id="childIcCardFareResult" class="number">
           </td>
         </tr>
       </tbody>
     </td> 
  </div>
</div>
</body>
</html>
