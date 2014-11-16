/**
 * 駅情報画面のスクリプト
 */
$(function() {
  $(document).ready(function() {
    // ヘッダーの初期化
    header.initialize();

    stationInfoBase.initMap();

    $('#selectExit').select2({
      width: 'resolve' ,
      dropdownAutoWidth: true
    });

    $('.tooltip').tooltipster({
      contentAsHTML: true,
      interactive: true
    });

    $('#selectExit').bind('change', function(event, ui) {
      var opt = $(this).find('option:selected');
      stationInfoBase.changeExit(opt.attr('lat'),
                                 opt.attr('long'), opt.attr('value'));
    });
  });
});
