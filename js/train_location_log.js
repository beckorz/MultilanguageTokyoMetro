/**
 * 列車ロケーション情報のスクリプト
 */
$(function() {
  var params = util.getQueryParam();
  var lang = '';
  if (params['lang']) {
    lang = params['lang'];
  }

  $(document).ready(function() {
    // ヘッダーの初期化
    header.initialize();
    /* 使いづらいので却下
    $('#timeSelect').select2({
      width: 200 ,
      dropdownAutoWidth : true
    });
    */
    $('#timeSelect').change(function() {
      $('.trainInfo').empty();
      $('.trainInfoTableData').remove();

      var selTime = $('#timeSelect').val();
      console.log(selTime);
        util.getJson(
          '/' + getAppName() + '/json/find_train_log',
          {
            updated: selTime,
            lang: lang
          },
          function(retcode, data) {
            if (retcode != 0) {
                return;
            }
            for (var i = 0; i < data.length; ++i) {
              var tbl = $('#trainInfoTable');
              var tr = $('<tr class="trainInfoTableData"/>');
              $('<td>' + data[i].created + '</td>').appendTo(tr);
              $('<td>' + data[i].valid + '</td>').appendTo(tr);
              $('<td><a href="' + data[i].trainUrl + '">' + data[i].trainNo + '</a></td>').appendTo(tr);
              $('<td><a href="' + data[i].railwayUrl + '">' + data[i].railway + '</a></td>').appendTo(tr);
              $('<td>' + data[i].trainType + '</td>').appendTo(tr);
              $('<td>' + data[i].direction + '</td>').appendTo(tr);
              $('<td>' + data[i].fromStationTitle + '</td>').appendTo(tr);
              if (data[i].toStationTitle) {
                $('<td>' + data[i].toStationTitle + '</td>').appendTo(tr);
              } else {
                $('<td/>').appendTo(tr);
              }
              $('<td>' + data[i].startingStationTitle + '</td>').appendTo(tr);
              $('<td>' + data[i].terminalStationTitle + '</td>').appendTo(tr);
              $('<td class="number">' + data[i].delay + '</td>').appendTo(tr);
              tr.appendTo(tbl);
            }

            $('.train').click(function(event) {
              alert('train' + $(this).text());
            });
            $('.tooltip').tooltipster({
              contentAsHTML: true,
              interactive: true
            });

          },
          function() {
            $.blockUI({ message: '<img src="/' + getAppName() + '/img/loading.gif" />' });
          },
          function() {
            $.unblockUI();
          }
        );
    }).keyup(function() {
      $(this).blur().focus();
    });
    if (params['updated']) {
      var updated = params['updated'].replace(/%20/g, ' ');
      console.log(updated);
      $('#timeSelect').val(updated);
      console.log($('#timeSelect').val());
      $('#timeSelect').trigger('change');
    }
  });
});
