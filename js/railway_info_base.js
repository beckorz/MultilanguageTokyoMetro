/**
 * 路線情報画面の共通部のスクリプト
 */
var railwayInfoBase = (function() {
  var paramRailway = 'odpt.Railway:TokyoMetro.Ginza';
  var params = util.getQueryParam();
  if (params['railway']) {
    paramRailway = params['railway'];
  }

  var paramLang = '';

  if (params['lang']) {
    paramLang = params['lang'];
  }

  function _getRailway() {
    return paramRailway;
  }

  function _getLang() {
    return paramLang;
  }

  /**
   * 路線変更処理
   * @param {string} railway 変更された路線
   */
  function _changeRailway(railway) {
    var url = location.href;
    url = url.substring(0, url.indexOf(url));
    url = url + '?railway=' + railway;
    if (paramLang) {
       url = url + '&lang=' + paramLang;
    }
    try {
      store.set('lastSelectedRailway', railway);
    } catch (e) {
      // 保存できなくても続行
    }
    location.href = url;
  }

  /**
   * 列車ロケーション情報のチェック
   * @param {function} beforeFunc 実行前の処理
   * @param {function} finishFunc 実行後の処理
   * @param {function} successFunc 成功時の処理
   */
  function _checkTrain(beforeFunc, finishFunc, successFunc) {
    console.log('checkTrain');
    // 現在路線に存在する列車ロケーション情報の取得
    util.getJson(
      '/' + getAppName() + '/json/get_train_location',
      {
        railway: paramRailway,
        lang: paramLang
      },
      function(retcode, data) {
        if (retcode != 0) {
            return;
        }
        $('.train_location').empty();
        for (var i = 0; i < data.length; ++i) {
          var loc = $('#' + data[i].location);
          if (loc.size() == 0) {
            console.log(data[i]);
            continue;
          }
          var train = $('<div class="train tooltip">').html(data[i].trainNumber + '(' + data[i].trainType + ') ');
          train.attr('trainType', data[i].trainType);
          train.attr('train_no', data[i].trainNumber);
          train.attr('direction', data[i].direction);
          if (data[i].delay > 0) {
            train.attr('style', 'background: #ff0000;color: #fff;');
          }
          var title = $('#trainMsgTmpl').render(data[i]);
          train.attr('title', title);
          loc.append(train);
        }
        successFunc();
      },
      beforeFunc,
      finishFunc
    );
  }

  var tmAutoUpdated;

  function _startAutoUpdate(beforeFunc, finishFunc, successFunc) {
    // 初期実行
    _checkTrain(beforeFunc, finishFunc, successFunc);

    tmAutoUpdated = setInterval(function() {
      _checkTrain(beforeFunc, finishFunc, successFunc);
    } , 1000 * 90);
  }

  function _stopAutoUpdate() {
    if (tmAutoUpdated) {
      clearInterval(tmAutoUpdated);
    }
  }

  return {
      getRailway: _getRailway,
      getLang: _getLang,
      changeRailway: _changeRailway,
      checkTrain: _checkTrain,
      startAutoUpdate: _startAutoUpdate,
      stopAutoUpdate: _stopAutoUpdate
  };
})();
