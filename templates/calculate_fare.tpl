<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&language={$gmaplang}"></script>
  <script type="text/javascript" src="/{$appName}/js/calculate_fare_base.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript" src="/{$appName}/js/calculate_fare.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body>
{include file='header.tpl'}
<div id="contents" {if $rtl}class="rtl"{/if}>
  <h1>{$label['title']}</h1>
  <div id="map_canvas" style="width: 100%; height: 400px"></div>

  <table class="normal">
      <tr>
          <th>
              {$label['fromStation']}
          </th>
          <td>
              <select class="iconSelect" id="fromRailwaySelect">
                  <option value="" selected=true></option>
                  {foreach from=$railways key=key item=item}
                      <option value="{$key}" img="{$item->icon}">{$item->title}</option>
                  {/foreach}
              </select>
              <select class="iconSelect" style="width:180px" id="fromStationSelect">
                  <option value="">　　　　　　 </option>
              </select>
          </td>
      </tr>
      <tr>
          <th>
              {$label['toStation']}
          </th>
          <td>
              <select class="iconSelect" id="toRailwaySelect">
                  <option value="" selected=true></option>
                  {foreach from=$railways key=key item=item}
                      <option value="{$key}" img="{$item->icon}">{$item->title}</option>
                  {/foreach}
              </select>
              <select class="iconSelect" style="width:180px" id="toStationSelect">
                  <option value="">　　　　　　 </option>
              </select>
          </td>
      </tr>
      <tr>
          <td colspan=2>
            <center>
            <button id="calculateFareBtn">{$label['calFare']}</button>
            </center>
          </td>
      </tr>
      <tr>
          <th>
              {$label['result']}
          </th>
          <td colspan=2>
              <table class="normal">
                <tr>
                      <td>{$label['ticketFareResult']}</td> 
                      <td><div id="ticketFareResult" class="number"></td>
                </tr>
                <tr>
                      <td>{$label['childTicketFareResult']}</td> 
                      <td><div id="childTicketFareResult" class="number"></td>
                </tr>
                <tr>
                      <td>{$label['icCardFareResult']}</td> 
                      <td><div id="icCardFareResult" class="number"></td>
                </tr>
                <tr>
                      <td>{$label['childIcCardFareResult']}</td> 
                      <td><div id="childIcCardFareResult" class="number"></td>
                </tr>
              <table>
          </td>
      </td>
  </table>
</div>
</body>
</html>
