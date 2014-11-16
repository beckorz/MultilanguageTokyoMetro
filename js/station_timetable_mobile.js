/**
 * 駅時刻表(モバイル)用のスクリプト
 */
$(function() {
  $(document).ready(function() {
    var curDayType = $('#currentDayType').text();
    console.log(curDayType);
    $(':mobile-pagecontainer').pagecontainer('change', '#' + curDayType, {transition: 'slideup'});

    $('#saturdays').find('.tooltip').on('tap', function(event) {
      util.showPopup('#saturdays_popup', $(this).attr('title'), event.target);
    });
    $('#holidays').find('.tooltip').on('tap', function(event) {
      util.showPopup('#holidays_popup', $(this).attr('title'), event.target);
    });
    $('#weekdays').find('.tooltip').on('tap', function(event) {
      util.showPopup('#weekdays_popup', $(this).attr('title'), event.target);
    });
  });
});
