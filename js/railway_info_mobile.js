/**
 * 路線情報画面(モバイル)のスクリプト
 */
$(function() {
  function beforeFunc() {
    $.mobile.loading('show');
  }

  function finishFunc() {
    $.mobile.loading('hide');
  }

  function successFunc() {
    $('.tooltip').on('tap', function(event) {
      util.showPopup('#train_popup', $(this).attr('title'), event.target);
    });
  }

  $(document).ready(function() {
    $('#railwaySelect').val(railwayInfoBase.getRailway()).selectmenu('refresh');

    railwayInfoBase.checkTrain(beforeFunc, finishFunc, successFunc);

    // 列車ロケーション情報の更新処理
    $('#checkTrain').on('tap', function() {
      railwayInfoBase.checkTrain(beforeFunc, finishFunc, successFunc);
    });

    // 路線情報の変更処理
    $('#railwaySelect').on('change', function() {
      var railway = $('#railwaySelect').val();
      railwayInfoBase.changeRailway(railway);
    });

    // 自動更新
    $('#autoUpdated').on('change', function() {
      if ($('#autoUpdated').val() === 'on') {
        railwayInfoBase.startAutoUpdate(beforeFunc, finishFunc, successFunc);
      } else {
        railwayInfoBase.stopAutoUpdate();
      }
    });
  });
});
