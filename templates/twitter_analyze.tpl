<!--
 このテンプレートは/{$appName}/twitter_analyzeのレイアウトを記述します.
-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$lang}">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <link type="text/css" media="screen" href="/{$appName}/js/jqcloud/jqcloud.css" rel="stylesheet" />
  {include file='common_include.tpl'}
  <script type="text/javascript" src="/{$appName}/js/jqcloud/jqcloud-1.0.4.min.js" ></script>
  <title>{$title}</title>
</head>
<body>
  <div id="contents">
    <h1>{$title}</h1>
    <h2>{$subTitle}</h2>
    <p>{$text}</p>
    <div id="tagcloud" style="width: 550px; height: 350px;"></div>
  </div>
  <script type="text/javascript">
  {literal}
  //<![CDATA[
      var word_array = JSON.parse('{/literal}{$data}{literal}');
      $(function() {
        // When DOM is ready, select the container element and call the jQCloud method, passing the array of words as the first argument.
        $("#tagcloud").jQCloud(word_array);
      });
  
  // ]]>
  {/literal}
  </script>
</body>
</html>
