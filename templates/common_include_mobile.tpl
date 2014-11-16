<!--
 このテンプレートはモバイル共通にインクルードする項目を記述します.
-->
  <link rel="stylesheet" href="/{$appName}/js/jquery-mobile/jquery.mobile-1.4.4.css">
  <link rel="stylesheet" href="/{$appName}/css/mobile.css">
  <script src="/{$appName}/js/jquery/jquery-1.11.1.min.js"></script>
  <script src="/{$appName}/js/jquery-mobile/jquery.mobile-1.4.4.min.js"></script>
  <script src="/{$appName}/js/jquery-mobile/jQuery.mobile.anchor.js"></script>
  <script type="text/javascript" src="/{$appName}/js/store/store.min.js"></script>
  <script type="text/javascript" src="/{$appName}/js/util.js?{($smarty.now|date_format:"%Y%m%d%H%M%S")}"></script>
  <script type="text/javascript">
     function getAppName() {
       return '{$appName}';
     }
  </script>
