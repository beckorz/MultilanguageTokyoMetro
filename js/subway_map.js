/**
 * 路線情報履歴（マップ）用のスクリプト
 */
$(function() {
  var params = util.getQueryParam();
  var lang = '';
  if (params['lang']) {
    lang = params['lang'];
  }
  $(document).ready(function() {
    var traceTrainId = {}; // 追跡対象の列車ID
    storeData = store.get('traceTrainId');
    if (storeData) {
      traceTrainId = storeData;
    }
    // ヘッダーの初期化
    header.initialize();

    $('.subway-map').subwayMap({ debug: false });
    $('#jquery-ui-resizable').resizable();

    var draging = false;
    var curX = 0;
    var curY = 0;

    $('canvas').on('touchstart mousedown', function(e) {
      if (e.originalEvent.touches) {
        var touch = e.originalEvent.touches[0];
        curX = touch.pageX;
        curY = touch.pageY;
      } else {
        curX = e.clientX;
        curY = e.clientY;
      }
      draging = true;
    });
    $('canvas').on('touchmove mousemove', function(e) {
      if (draging) {
        var d = $('#subway-map-contents');
        var nextX;
        var nextY;
        if (e.originalEvent.touches) {
          var touch = e.originalEvent.touches[0];
          nextX = touch.pageX;
          nextY = touch.pageY;
        } else {
          nextX = e.clientX;
          nextY = e.clientY;
        }
        d.scrollTop(d.scrollTop() + (curY - nextY));
        d.scrollLeft(d.scrollLeft() + (curX - nextX));

        curX = nextX;
        curY = nextY;
        draging = true;
      }
    });
    $('canvas').on('touchend touchcancel mouseup mouseout', function(e) {
      draging = false;
    });

    /* 使いづらいので却下
    $('#timeSelect').select2({
      width: 200 ,
      dropdownAutoWidth : true
    });
    */

    function getTrainImage(id, delay) {
      if (traceTrainId[id] && delay > 0) {
        return '/' + getAppName() + '/img/trainicon_traced_delay.gif';
      } else if (traceTrainId[id]) {
        return '/' + getAppName() + '/img/trainicon_traced.gif';
      } else if (delay > 0) {
        return '/' + getAppName() + '/img/trainicon_delay.png';
      } else {
        return '/' + getAppName() + '/img/trainicon.png';
      }
    }

    $('#timeSelect').change(function() {
      $('.trainInfo').empty();
      $('.trainInfoTableData').remove();

      var selTime = $('#timeSelect').val();
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
              var key = data[i].fromStation;
              if (data[i].toStation) {
                key += '_' + data[i].toStation;
                if ($('#' + key).size() == 0) {
                  key = data[i].toStation + '_' + data[i].fromStation;
                  if ($('#' + key).size() == 0) {
                    console.log('not found' + key);
                    continue;
                  }
                }
              }
              var el = $('#' + key);
              data[i].chkTrace = false;
              var msg = $('#trainMsgTmpl').render(data[i]);
              var img = $('<img id="' + data[i].sameAs + '" delay="' + data[i].delay + '" class="train tooltip"/>');
              img.attr('src', getTrainImage(data[i].sameAs, data[i].delay));
              img.attr('title', msg);
              img.appendTo(el);

              if (!data[i].startingStationTitle) {
                console.log(data[i].startingStation);
              }
            }

            $('.train').click(function(event) {
              //alert("train"+$(this).text());
            });
            $('.tooltip').tooltipster({
              contentAsHTML: true,
              interactive: true,
              functionReady: function() {
                var id = util.getEscapedId($(this).attr('id'));
                var item = $('#chk_' + id);
                if (traceTrainId[item.attr('sameAs')]) {
                  item.attr('checked', 'on');
                }

                $('.track').change(function(event) {
                  var id = $(event.target).attr('sameAs');
                  if (event.target.checked) {
                    traceTrainId[$(event.target).attr('sameAs')] = true;
                  } else {
                    delete traceTrainId[$(event.target).attr('sameAs')];
                  }
                  $('#' + util.getEscapedId(id)).attr('src', getTrainImage(id, $('#' + util.getEscapedId(id)).attr('delay')));
                  try {
                    store.set('traceTrainId', traceTrainId);
                  } catch (e) {
                    // 保存できなくても続行
                  }
                });
              }
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
  });
});
