<!--
 このテンプレートは/{$appName}/train_infoのレイアウトを記述します.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <title>{$label['title']}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/train_info.js"></script>
</head>
<body>
{include file='header.tpl'}

<div id="contents" {if $rtl}class="rtl"{/if}>
  <div align="right">  <b>{$label['check_traininfolog']}:</b>{$check_traininfolog}</div>
  <div id="tabs">
    <ul>
      {foreach from=$traininfos item=info}
        <li><a href="#{$info['railwayId']}"><img src="{$info['railwayIcon']}" style="vertical-align: middle;" width="20" height="20"  alt=""/>{$info['railwayName']}</a></li>
      {/foreach}
    </ul>
    {foreach from=$traininfos item=info}
        <div id="{$info['railwayId']}">
          <h1><img src="{$info['railwayIcon']}"  style="margin-top: -5px;" width="32" height="32" alt=""/> {$info['railwayName']}</h1>
          <table class="normal">
              <tr>
                   <th scope="col">{$label['created']}</th>
                   <th scope="col">{$label['origin']}</th>
                   <th scope="col">{$label['updated']}</th>
                   <th scope="col">{$label['status']}</th>
                   <th scope="col">{$label['information']}</th>
              </tr>
              {foreach from=$info['log'] item=log}
                  <tr>
                       <td>{$log['created']}</td>
                       <td>{$log['origin']}</td>
                       <td>{$log['updated']}</td>
                       <td>{$log['status']}</td>
                       <td>{$log['information']}</td>
                  </tr>
              {/foreach}
          </table>
        </div>
    {/foreach}
  </div>
</div>
</body>
</html>
