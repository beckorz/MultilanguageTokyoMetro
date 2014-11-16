/**
 * 列車時刻表用のスクリプト
 */
$(function() {
  $(document).ready(function() {
    // ヘッダーの初期化
    header.initialize();

    var curDayType = $('#currentDayType').text();
    var index = $('#tabs a[href="#odpt:' + curDayType + '"]').parent().index();
    $('#tabs').tabs({active: index});
  });
});
