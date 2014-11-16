/**
 * 列車時刻表（モバイル用）のスクリプト
 */
$(function() {
  $(document).ready(function() {
    var curDayType = $('#currentDayType').text();
    $(':mobile-pagecontainer').pagecontainer('change', '#' + 'odpt:' + curDayType, {transition: 'slideup'});
  });
});
