<!-- 
このテンプレートはツイッター操作を行うテンプレートです
仕様する引数は下記の通りです.
 hash                 検索に用いるハッシュタグ
 searchHashTitle      ハッシュタグで検索するボタンのタイトル
 alanyzeTermHashTitle ハッシュタグの形態素解析を行うボタンのタイトル
 searchHashTitle      ハッシュタグで検索するボタンのタイトル
 alanyzeTermHashTitle ハッシュタグの形態素解析を行うボタンのタイトル
 alanyzePosTitle      位置情報による検索を行うボタンのタイトル。省略した場合はボタン自体が表示されない。
 lat                  位置情報(省略可)
 long                 位置情報(省略可) 
 posLabel             位置を表す名称(省略可)
-->
<div class="twitter">
  <BR>
  <a href="https://twitter.com/hashtag/{$hash}?src=hash" rel="external" class="ui-btn" target="searchHashTitle">{$searchHashTitle}</a>
  <a href="/{$appName}/page/twitter_analyze?hash={$hash}&title={urlencode($alanyzeTermHashTitle)}&lang={$lang}" class="ui-btn" rel="external" target="alanyzeTermHashTitle">{$alanyzeTermHashTitle}</a>
  {if $alanyzePosTitle}
    <a href="/{$appName}/page/twitter_analyze?hash={$hash}&title={urlencode($alanyzePosTitle)}&lat={$lat}&long={$long}&posLabel={urlencode($posLabel)}&lang={$lang}" class="ui-btn" rel="external" target="alanyzeTermHashTitle">{$alanyzePosTitle}</a>
  {/if}
  {literal}
    <a href="https://twitter.com/intent/tweet?button_hashtag={/literal}{$hash}{literal}" class="twitter-hashtag-button" data-size="large" data-related="mima_ita">Tweet #{/literal}{$hash}{literal}</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script> 
  {/literal}
</div>