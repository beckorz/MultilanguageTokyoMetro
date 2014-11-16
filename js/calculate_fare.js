/**
 * 運賃計算画面用のスクリプト
 */
$(function() {
  function format(state) {
    if (!state.id) {
      return state.text;
    }
    return "<img style='vertical-align: middle' class='flag' width='20' height='20' src='" + $(state.element[0]).attr('img') + "'/>" + state.text;
  }

  function beforeFunc() {
    $.blockUI({ message: '<img src="/' + getAppName() + '/img/loading.gif" />' });
  }

  function finishFunc() {
    $.unblockUI();
  }

  $(document).ready(function() {
    // ヘッダーの初期化
    header.initialize();

    calcFareBase.initMap(beforeFunc, finishFunc);

    $('.iconSelect').select2({
      formatResult: format,
      formatSelection: format,
      escapeMarkup: function(m) { return m; },
      width: 'resolve' ,
      dropdownAutoWidth: true
    });

    $('#calculateFareBtn').button().click(function() {
      calcFareBase.calculateFare(beforeFunc, finishFunc);
    });
  });
});
