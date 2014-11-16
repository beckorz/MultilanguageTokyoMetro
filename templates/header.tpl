<!--
 このテンプレートは共通ヘッダーのレイアウトを記述します.
-->
<div id="header" >
  <!--言語選択用のセレクトボックス-->
  <div class="headItem {if $rtl}rtl{/if}">
    <img src="/{$appName}/img/world.png" class="tooltip" title="{$headLabel['selectLang']}" alt=""/>
    <select id="langSelect">
      {foreach from=$langList key=key item=item}
        {if $lang eq $key}
          <option value="{$key}" selected>{$item->title}</option>
        {else}
          <option value="{$key}" >{$item->title}</option>
        {/if}
      {/foreach}
    </select>
  </div>

  <!--スタートページへのリンク-->
  <div class="headItem {if $rtl}rtl{/if}">
    <a href="/{$appName}?lang={$lang}" >
      <img src="/{$appName}/img/home.png" class="tooltip btn_icon" title="{$headLabel['start']}" alt=""></img>
    </a>
  </div>

  <!--路線情報へのリンク-->
  <div class="headItem {if $rtl}rtl{/if}">
    <a id="headerRailwayLink" href="/{$appName}/page/railway_info?lang={$lang}" >
      <img  src="/{$appName}/img/railway.png" class="tooltip btn_icon" title="{$headLabel['railway']}" alt=""></img>
    </a>
  </div>
  
  <!--現在地から列車情報の検索へのリンク-->
  <div class="headItem {if $rtl}rtl{/if}">
    <a href="/{$appName}/page/pos_to_train_no?lang={$lang}" >
      <img  src="/{$appName}/img/position.png" class="tooltip btn_icon" title="{$headLabel['pos_to_train_no']}" alt=""></img>
    </a>
  </div>

  <!--運賃計算へのリンク-->
  <div class="headItem {if $rtl}rtl{/if}">
    <a href="/{$appName}/page/calculate_fare?lang={$lang}" >
      <img  src="/{$appName}/img/money.png" class="tooltip btn_icon" title="{$headLabel['calc_fare']}" alt=""></img>
    </a>
  </div>

  <!--履歴へのリンク-->
  <div class="headItem submenu {if $rtl}rtl{/if}">
    <img src="/{$appName}/img/history.png" class="btn_icon" alt=""></img>
    <div class="submenuItem">
      <ul>
        <li><a href="/{$appName}/page/train_info?lang={$lang}">{$headLabel['train_info_log']}</a></li>
        <li><a href="/{$appName}/page/train_location_log?lang={$lang}">{$headLabel['train_location_log']}</a></li>
        <li><a href="/{$appName}/page/subway_map?lang={$lang}">{$headLabel['train_location_log_map']}</a></li>
        <li><a href="/{$appName}/page/translation_log?lang={$lang}">{$headLabel['translation_log']}</a></li>
      </ul>
    </div>
  </div>

  <!--ローカライズ編集へのリンク-->
  <div class="headItem {if $rtl}rtl{/if}">
    <a href="/{$appName}/page/translation?lang={$lang}" >
      <img src="/{$appName}/img/edit.png" class="tooltip btn_icon" title="{$headLabel['translation']}" alt=""></img>
    </a>
  </div>

  <!-- 電光掲示板 -->
  <div class="headItem">
    <div class="ledText" >
      <div class="ledTextContents {if $rtl}rtl{/if}" ><span　class="railway">Now loading.</span></div>
    </div>
  </div>

  <!-- 以降右からのメニュー============================= -->
  <div class="headerRightGroup" style="float:right;padding:0 10px;">
    <!-- ログイン -->
    <div class="rightItem {if $rtl}rtl{/if}" >
      {if $user}
        <a href="/{$appName}/logout?lang={$lang}">{$headLabel['logout']}</a>
      {else}
         <a href="/{$appName}/login?lang={$lang}">{$headLabel['login']}</a>
      {/if}
    </div>

    <!-- 連絡先 -->
    <div class="rightItem {if $rtl}rtl{/if}">
      <span>  {$headLabel['contact']}:</span><a href="https://twitter.com/mima_ita">mima_ita</a>
    </div>
  </div>


</div> 
