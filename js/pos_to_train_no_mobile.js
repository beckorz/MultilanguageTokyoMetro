/**
 * 現在位置から列車検索画面（モバイル）のスクリプト
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
  /**
   * 現在地からの列車検索
   */
  $(document).ready(function() {
    var map = null;
    var circle = null;
    var curPos = new google.maps.LatLng(35.709984, 139.810703);
    var stationData = null;
    var trainData = null;
    var watchId = null;

    var opts = {
      zoom: 16,
      center: curPos,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map_canvas'), opts);

    /**
     * googleマップに選択した範囲の円を記述する
     */
    function drawCircle() {
      var r = parseInt($('#selectRadius').val());
      var circleOpts = {
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 1,
        fillColor: '#FF0000',
        center: curPos,
        map: map,
        radius: r
      };
      if (circle) {
        circle.setMap(null);
      }
      circle = new google.maps.Circle(circleOpts);

    }

    /**
     * 現在地の取得
     */
    $('#flipGetCurPos').on('change', function() {
      if (!map) {
        return;
      }
      if (!navigator.geolocation) {
        util.showPopupDialog('not support geolocation.');
        return;
      }
      if ($('#flipGetCurPos').val() === 'on') {
        watchId = navigator.geolocation.watchPosition(function(position) {
          curPos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
          map.panTo(curPos);
          drawCircle();
        },function(positionError) {
          util.showPopupDialog(positionError.message);
        },{
            maximumAge: 250,
            enableHighAccuracy: true
          }
        );
      } else {
        if (watchId != null) {
          navigator.geolocation.clearWatch(watchId);
          watchId = null;
        }
      }
    });

    /**
     * 検索位置の半径の変更
     */
    $('#selectRadius').on('change', function() {
      if (!map) {
        return;
      }
      if (!curPos) {
        return;
      }
      drawCircle();
    });

    /**
     * 駅検索ボタンの押下処理
     */
    $('#btnSearchStation').on('tap', function() {
      if (!curPos) {
        return;
      }
      util.getJson(
        '/' + getAppName() + '/json/get_station_place?lang=' + lang,
        {lat: curPos.lat() , lon: curPos.lng(), radius: parseInt($('#selectRadius').val()) },
        function(retcode, data) {
          if (retcode != 0) {
              return;
          }
          $('#selectStation').empty();
          $('#selectDirection').empty();
          $('<option></option>').appendTo('#selectStation');
          for (var i = 0; i < data.length; ++i) {
            $('<option value="' + data[i].sameAs + '" data-image="' + data[i].stationCodeImage + '">' + data[i].title + '</option>').appendTo('#selectStation');
          }
          $(':mobile-pagecontainer').pagecontainer('change', '#stationPage', { transition: 'slidedown'});
          stationData = data;
        }, function() {
          $.mobile.loading('show');
        }, function() {
          $.mobile.loading('hide');
        }
      );
    });

    /**
     * 選択駅の変更
     */
    $('#selectStation').on('change', function() {
      if (!stationData) {
        return;
      }
      var sel = $('#selectStation').val();
      if (!sel) {
        return;
      }
      $('#selectDirection').empty();
      for (var i = 0; i < stationData.length; ++i) {
        if (stationData[i].sameAs == sel) {
          console.log(stationData[i].directions);
          for (var j = 0; j < stationData[i].directions.length; ++j) {
            $('<option value="' + stationData[i].directions[j].direction + '">' + stationData[i].directions[j].directionTitle + '</option>').appendTo('#selectDirection');
          }
          break;
        }
      }
    });

    /**
     * 時刻表の取得ボタン
     */
    $('#btnSearchStationTimeTable').on('tap', function() {
      var station = $('#selectStation').val();
      if (!station) {
        return;
      }
      var direction = $('#selectDirection').val();
      if (!direction) {
        return;
      }
      util.getJson(
        '/' + getAppName() + '/json/get_station_timetable?lang=' + lang,
        {
          station: station,
          direction: direction,
          date: Math.round(new Date().getTime() / 1000)
        },
        function(retcode, data) {
          console.log(data);
          if (retcode != 0) {
              return;
          }
          $('#selectTrain').empty();
          $('<option></option>').appendTo('#selectTrain');
          for (var i = 0; i < data.length; ++i) {
            $('<option value="' + data[i].train + '" url="' + data[i].trainUrl + '">' + data[i].departureTime + ' ' + data[i].destinationStationTitle + ' ' + data[i].trainNumber + '</option>').appendTo('#selectTrain');
          }
          $(':mobile-pagecontainer').pagecontainer('change', '#stationTimetablePage', { transition: 'slidedown'});
          trainData = data;
        }, function() {
          $.mobile.loading('show');
        }, function() {
          $.mobile.loading('hide');
        }

      );
    });

    /**
     * 列車時刻表の検索
     */
    $('#btnSearchTrain').on('tap', function() {
      var train = $('#selectTrain option:selected');
      console.log(train);
      var url = train.attr('url');
      if (!url) {
        return;
      }
      //$(':mobile-pagecontainer').pagecontainer('change', url, { transition: 'slidedown'});
      window.location = url;
    });
  });
});
