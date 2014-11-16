<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/jquery/jsrender.min.js"></script>
  <script type="text/javascript" src="/{$appName}/js/jquery/jquery.subwayMap-0.5.0.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript" src="/{$appName}/js/subway_map.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>

  <script id="trainMsgTmpl" type="text/x-jsrender">
    <div {if $rtl}class="rtl"{/if}>
    <a href="{literal}{{:trainUrl}}{/literal}">{literal}{{:trainNo}}{/literal}</a>({literal}{{:trainType}}{/literal})
    <input id="chk_{literal}{{:sameAs}}{/literal}" sameAs="{literal}{{:sameAs}}{/literal}"  class="track" type="checkbox"> {$label['trace']}</input><BR>
    {literal}{{:startingStationTitle}}=>{{:terminalStationTitle}}{/literal}<br>
    from : {literal}{{:fromStationTitle}}{/literal}<br>
    to : {literal}{{:toStationTitle}}{/literal}<br>
    {$label['delay']} : {literal}{{:delay}}{/literal}<br>
    {$label['created']} :{literal}{{:created}}{/literal}<br>
    {$label['valid']} :{literal}{{:valid}}{/literal}<br>
    </div>
  </script>
</head>
<body>
{include file='header.tpl'}

  <div id="contents" >
    <h1 >{$label['title']}</h1>
    <div {if $rtl}class="rtl"{/if}>
    {$label['dataDate']}：<select id="timeSelect">
      <option value="" selected=true></option>
      {foreach from=$selectdates key=key item=seldate}
          <option value="{$seldate}">{$seldate}</option>
      {/foreach}
    </select>
    </div>
    <div id="jquery-ui-resizable" style="width:100%;height:500px" class="ui-widget-content">
      <div id="subway-map-contents"  style="width:100%;height:100%;overflow:auto;border:solid 1px graye;position: relative;"> 
        <div class="subway-map" data-columns="72" data-rows="45" data-cellSize="30" data-legendId="legend" data-textClass="text" data-gridNumbers="true" data-grid="true" data-lineWidth="8">
          {foreach from=$railways item=railway}
            <ul data-color="{$railway['color']}" data-label="{$railway['title']}">
              {foreach from=$railway['stationOrder'] item=pt}
                <li station-code="{$pt['idName']}" data-coords="{$pt['x']},{$pt['y']}" data-marker="{$pt['marker']}" data-labelPos="s">
                  {if $pt['marker'] eq 'interchange'}
                    {$pt['title']}
                  {/if}
                </li>
              {/foreach}
            </ul>
          {/foreach}
        </div>
      </div>
    </div>
  </div>
</body>
</html>
