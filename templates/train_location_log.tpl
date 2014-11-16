<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/train_location_log.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
</head>
<body>
{include file='header.tpl'}
  <div id="contents" {if $rtl}class="rtl"{/if}>
    <h1 >{$label['title']}</h1>
    {$label['dataDate']}：<select id="timeSelect">
      <option value="" selected=true></option>
      {foreach from=$selectdates key=key item=seldate}
          <option value="{$seldate}">{$seldate}</option>
      {/foreach}
    </select>

    <table id="trainInfoTable" class="normal">
      <tr>
        <th scope="col">{$label['created']}</th>
        <th scope="col">{$label['valid']}</th>
        <th scope="col">{$label['TrainNo']}</th>
        <th scope="col">{$label['Railway']}</th>
        <th scope="col">{$label['trainType']}</th>
        <th scope="col">{$label['direction']}</th>
        <th scope="col">{$label['from']}</th>
        <th scope="col">{$label['to']}</th>
        <th scope="col">{$label['StatingStation']}</th>
        <th scope="col">{$label['TerminalStation']}</th>
        <th scope="col">{$label['delay']}</th>
      </tr>
    </table>
  </div>
</body>
</html>
