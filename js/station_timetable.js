/**
 * 駅時刻表用のスクリプト
 */
$(function() {
  $(document).ready(function() {
    // ヘッダーの初期化
    header.initialize();

    var curDayType = $('#currentDayType').text();
    var index = $('#tabs a[href="#' + curDayType + '"]').parent().index();
    $('#tabs').tabs({active: index});
    $('.tooltip').tooltipster({
     contentAsHTML: true,
     interactive: true
    });
  });
});
