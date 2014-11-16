/**
 * 列車運行情報履歴用のスクリプト
 */
$(function() {
  var params = util.getQueryParam();
  var railway = '';
  if (params['railway']) {
    railway = params['railway'];
  }
  $(document).ready(function() {
    // ヘッダーの初期化
    header.initialize();

    if (railway) {
      var index = $('#tabs ul').index($('#' + railway));
      var railwayId = railway;
      railwayId = railway.replace(/\./g, '\\.');
      railwayId = railwayId.replace(/:/g, '\\:');
      index = $('#tabs a[href="#' + railwayId + '"]').parent().index();
      $('#tabs').tabs({active: index});
    } else {
      $('#tabs').tabs();
    }
  });
});
