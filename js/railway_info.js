/**
 * 路線情報画面のスクリプト
 */
$(function() {
  function format(state) {
    if (!state.id) return state.text; // optgroup
    return "<img style='vertical-align: middle' class='flag' width='20' height='20' src='" + $(state.element[0]).attr('img') + "'/>" + state.text;
  }

  function beforeFunc() {
    $.blockUI({ message: '<img src="/' + getAppName() + '/img/loading.gif" />' });
  }

  function finishFunc() {
    $.unblockUI();
  }

  function successFunc() {
    $('.tooltip').tooltipster({
      contentAsHTML: true,
      interactive: true
    });
  }
  $(document).ready(function() {
    // ヘッダーの初期化
    header.initialize();

    $('#railwaySelect').val(railwayInfoBase.getRailway());
    $('#railwaySelect').select2({
      formatResult: format,
      formatSelection: format,
      escapeMarkup: function(m) { return m; },
      width: 'resolve' ,
      dropdownAutoWidth: true
    });

    railwayInfoBase.checkTrain(beforeFunc, finishFunc, successFunc);

    $('#checkTrain').button().click(function() {
      railwayInfoBase.checkTrain(beforeFunc, finishFunc, successFunc);
    });

    $('#autoUpdated').bind('change', function(event, ui) {
      if ($(this).is(':checked')) {
        railwayInfoBase.startAutoUpdate(beforeFunc, finishFunc, successFunc);
      } else {
        railwayInfoBase.stopAutoUpdate();
      }
    });

    $('#railwaySelect').bind('change', function(event, ui) {
      var railway = $('#railwaySelect').val();
      railwayInfoBase.changeRailway(railway);
    });
  });
});
