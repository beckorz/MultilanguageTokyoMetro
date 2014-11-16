/**
 * スタート画面(モバイル)のスクリプト
 */
$(function() {
  var lang;
  var params = util.getQueryParam();
  if (params['lang']) {
    lang = params['lang'];
  } else {
    // ローカルストレージからLANG取得
    lang = store.get('lang');
    if (lang) {
      var url = window.location;
      if (params.length == 0) {
        url = '?lang=' + lang;
      } else {
        url = '&lang=' + lang;
      }
      window.location = url;
    }
  }

  $(document).ready(function() {
    var lastSel = store.get('lastSelectedRailway');
    if (lastSel) {
      $('#railwayLink').attr('href', $('#railwayLink').attr('href') + '&railway=' + lastSel);
    }


    $('#langSelect').on('change', function() {
      lang = $('#langSelect').val();
      // ローカルストレージの保存
      try {
           store.set('lang', lang);
      } catch (e) {
           // 保存できなくても続行
      }
      var url = window.location.protocol + '//' + window.location.host + window.location.pathname;
      var i = 0;
      for (var prop in params) {
        console.log(prop);
        if (prop == 'lang') {
          continue;
        }
        if (i == 0) {
          url += '?' + prop + '=' + params[prop];
        } else {
          url += '&' + prop + '=' + params[prop];
        }
        ++i;
      }
      if (i == 0) {
        url += '?lang=' + lang;
      } else {
        url += '&lang=' + lang;
      }

      window.location = url;
    });

    $('#latestTrainInfo').on('tap', function() {
      console.log('latestTrainInfo');
      util.getJson(
        '/' + getAppName() + '/json/get_train_info',
        {
          lang: lang
        },
        function(retcode, data) {
          console.log(data);
          if (retcode != 0) {
              return;
          }
          $('#latestTrainInfoResult').empty();
          var ul = $('<ul></ul>');
          for (var i = 0; i < data.length; ++i) {
            var li = $('<li></li>');
            console.log(data[i]);
            $('<span class="railway">【' + data[i].railway + '】</span>').appendTo(li);
            if (data[i].status) {
              $('<span class="status">【' + data[i].status + '】</span>').appendTo(li);
            }
            $('<span class="information">' + data[i].informationText + '</span>').appendTo(li);
            li.appendTo(ul);
          }
          ul.appendTo($('#latestTrainInfoResult'));
        }, function() {
          $.mobile.loading('show');
        }, function() {
          $.mobile.loading('hide');
        }
      );
    });
  });
});
