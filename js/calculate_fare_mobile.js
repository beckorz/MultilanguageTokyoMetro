/**
 * 運賃計算画面（モバイル）のスクリプト
 */
$(function() {
  function beforeFunc() {
    $.mobile.loading('show');
  }
  function finishFunc() {
    $.mobile.loading('hide');
  }

  $(document).ready(function() {
    calcFareBase.initMap(beforeFunc, finishFunc);

    $('#calculateFareBtn').on('tap', function() {
      calcFareBase.calculateFare(beforeFunc, finishFunc);
    });
  });
});
