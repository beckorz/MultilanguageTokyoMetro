/**
 * ヘッダのスクリプト
 */
var header = (function() {
  var lang;
  var params = util.getQueryParam();

  /**
   * 初期化処理
   */
  function _initialize() {
    // 言語情報の取得
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
    var lastSel = store.get('lastSelectedRailway');
    if (lastSel) {
      $('#headerRailwayLink').attr('href', $('#headerRailwayLink').attr('href') + '&railway=' + lastSel);
    }
    var submenu = $('#header').find('.submenu');
    submenu.hover(function() {
      var item = $(this).find('.submenuItem');
      item.slideDown(200);
    },function() {
      var item = $(this).find('.submenuItem');
      item.hide();
    });
    // ツールチップの表示
    $('#header').find('.tooltip').tooltipster({
      contentAsHTML: true,
      interactive: true
    });
    //

    /* 固定ヘッダはモバイルで安定しないので却下。
    // #を含むリンクの場合、固定ヘッダを考慮した位置に移動する
    // (外部リンク用）
    if (window.location.hash.match(/^#/)) {
        var href= window.location.hash;
        href = href.replace(/\./g,'\\.');
        href = href.replace(/\:/g,'\\:');
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top- ($('#header').height()+10); //ヘッダの高さ分位置をずらす
        $("html, body").animate({scrollTop:position}, 550, "swing");
    }

    // #を含むリンクの場合、固定ヘッダを考慮した位置に移動する
    // (内部リンク用）
    $('a[href^=#]').click(function(){
        var href= $(this).attr("href");
        href = href.replace(/\./g,'\\.');
        href = href.replace(/\:/g,'\\:');
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top - ($('#header').height()+10); //ヘッダの高さ分位置をずらす
        $("html, body").animate({scrollTop:position}, 550, "swing");
        return false;
    })
    */

    // 言語選択時の処理
    $('#langSelect').select2({
      width: 150 ,
      dropdownAutoWidth: true
    });

    // 電光掲示板の大きさを変更
    function adjustLedText() {
      var led = $('#header').find('.ledText');
      var rightItem = $('#header').find('.headerRightGroup');
      var setWd = rightItem.offset().left - led.offset().left;
      led.width(setWd);
    }

    $(window).resize(function() {
      console.log('adjust');
      adjustLedText();
    });
    $(window).load(function() {
      // 処理
      adjustLedText();
    });
    function checkTrainInfoLog() {
      console.log('checkTrainInfoLog');
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
          var ledtxt = $('.ledText');
          var ledtxtcContents = $('.ledTextContents');
          var ledhtml = ledtxtcContents.get(0).outerHTML;
          ledtxtcContents.remove();
          ledtxtcContents = $(ledhtml);
          ledtxtcContents.empty();
          var strCnt = 0;
          var margin = '　　　　　　　　　　　　　　　　　　　　　';
          for (var i = 0; i < data.length; ++i) {
            $('<span class="railway">【' + data[i].railway + '】</span>').appendTo(ledtxtcContents);
            if (data[i].status) {
              $('<span class="status">【' + data[i].status + '】</span>').appendTo(ledtxtcContents);
            }
            $('<span class="information">' + data[i].informationText + margin + '</span>').appendTo(ledtxtcContents);
            strCnt += data[i].railway.length + data[i].status.length + data[i].informationText.length + margin.length;
          }
          var sec = strCnt / 3;
          ledtxtcContents.attr('style' , '-webkit-animation-duration: ' + sec + 's;-moz-animation-duration: ' + sec + 's;animation-duration: ' + sec + 's;');
          ledtxtcContents.appendTo(ledtxt);
        }
      );
    }
    checkTrainInfoLog();
    setInterval(checkTrainInfoLog, 90000);

    $('#langSelect').change(function() {
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
    }).keyup(function() {
      $(this).blur().focus();
    });
  }

  /**
   * 選択中の言語を取得
   */
  function _getLang() {
    return lang;
  }

  return {
      initialize: _initialize,
      getLang: _getLang
  };
})();
