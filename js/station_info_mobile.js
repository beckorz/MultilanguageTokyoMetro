/**
 * 駅情報（モバイル用）のスクリプト
 */
$(function() {
  $(document).ready(function() {
    $('.tooltip').on('tap', function(event) {
      util.showPopup('#hasAssistant_popup', $(this).attr('title'), event.target);
    });

    $('#exit').on('pageshow', function() {
        console.log('pageshow');
        //mapが非表示のときに初期化されると表示がおかしくなるので、ページきりかえ時にリフレッシュ
        //http://www.softel.co.jp/blogs/tech/archives/4069
        stationInfoBase.refreshMap();
    });

	// ページ内リンクの有効可
    $(document).anchor({ duration: 'normal', offset: 150, speed: 100});

    $('#exit').on('pagecreate', function() {

      stationInfoBase.initMap();

      $('#selectExit').on('change', function() {
        var opt = $(this).find('option:selected');
        stationInfoBase.changeExit(opt.attr('lat'),
                                   opt.attr('long'), opt.attr('value'));
      });
    });
  });
});
