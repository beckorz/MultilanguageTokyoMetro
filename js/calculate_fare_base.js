/**
 * 路線情報画面の共通部のスクリプト
 */
var calcFareBase = (function() {
  var params = util.getQueryParam();
  var lang = '';
  if (params['lang']) {
    lang = params['lang'];
  }
  var map;

  function _initMap(beforeFunc, finishFunc) {
    var latlng = new google.maps.LatLng(35.709984, 139.810703);
    var opts = {
      zoom: 11,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById('map_canvas'), opts);
    // 乗り換え案内レイヤ
    //var transitLayer = new google.maps.TransitLayer();
    //transitLayer.setMap(map);
    $('#fromRailwaySelect').val('');
    $('#toRailwaySelect').val('');

    var fromPathList = [];
    var fromInfoWin;
    var toPathList = [];
    var toInfoWin;

    addChangeEvent(beforeFunc, finishFunc, '#fromRailwaySelect', '#fromStationSelect', map, fromPathList, fromInfoWin, 'From:', '#FF0000');
    addChangeEvent(beforeFunc, finishFunc, '#toRailwaySelect', '#toStationSelect', map, toPathList, toInfoWin, 'To:', '#0000FF');

  }
  function addChangeEvent(beforeFunc, finishFunc, railwaySelId, stationSelId, map, pathList, infoWin, memo, strokeColor) {
    $(railwaySelId).bind('change', function(event, ui) {
      for (var i = 0; i < pathList.length; ++i) {
        pathList[i].setMap(null);
      }
      var railway = $(railwaySelId).val();
      if (!railway) {
          return;
      }
      // 路線形状の選択
      util.getJson(
        '/' + getAppName() + '/json/get_railway_region',
        {railway: railway, lang: lang},
        function(retcode, data) {
          if (retcode != 0) {
              return;
          }
          for (var item in data) {
            console.log(item);
            for (var i = 0; i < data[item].coordinates.length; ++i) {
              var coordinates = [];
              for (var j = 0; j < data[item].coordinates[i].length; ++j) {
                coordinates.push(new google.maps.LatLng(
                  data[item].coordinates[i][j][1],
                  data[item].coordinates[i][j][0])
                );
              }
              var path = new google.maps.Polyline({
                path: coordinates,
                strokeColor: strokeColor,
                strokeOpacity: 0.5,
                strokeWeight: 4
              });
              path.setMap(map);
              pathList.push(path);
            }
          }
        },
        beforeFunc,
        finishFunc
      );

      // 駅名の一覧
      util.getJson(
        '/' + getAppName() + '/json/get_station_list',
        {railway: railway, geo: 1, station_code: 1, lang: lang},
        function(retcode, data) {
          if (retcode != 0) {
              return;
          }
          $(stationSelId).empty();
          $(stationSelId).append($('<option>').html('').val(''));
          data.sort(
            function(a, b) {
              if (a.stationCode < b.stationCode) return -1;
              if (a.stationCode > b.stationCode) return 1;
              return 0;
            }
          );
          for (var i = 0; i < data.length; ++i) {
            var opt = $('<option>').html(data[i].title).val(data[i].sameAs);
            opt.attr('long', data[i].long);
            opt.attr('lat', data[i].lat);
            opt.attr('img', data[i].stationCodeImage);
            opt.attr('stationCode', data[i].stationCode);
            $(stationSelId).append(opt);
          }
        }
      );
    });

    $(stationSelId).bind('change', function(event, ui) {
      if (infoWin) {
        infoWin.close();
      }
      var selId = $(this).val();
      if (!selId) {
        return;
      }
      var opt = $(this).find('[value="' + selId + '"]');

      infoWin = new google.maps.InfoWindow({
         position: new google.maps.LatLng(opt.attr('lat'),
                                          opt.attr('long')),
         content: memo + opt.text()
      });
      infoWin.setMap(map);
    });
  }
  function _calculateFare(beforeFunc, finishFunc) {
    var fromStation = $('#fromStationSelect').val();
    var toStation = $('#toStationSelect').val();

    if (!fromStation) {
      return;
    }
    if (!toStation) {
      return;
    }

    util.getJson(
      '/' + getAppName() + '/json/calculate_fare',
      {from: fromStation, to: toStation, lang: lang},
      function(retcode, data) {
        if (retcode != 0) {
            return;
        }
        for (var i = 0; i < data.length; ++i) {
          if (data[i]['odpt:fromStation'] != fromStation) {
            continue;
          }
          if (data[i]['odpt:toStation'] != toStation) {
            continue;
          }
          $('#ticketFareResult').text(data[i]['odpt:ticketFare']);
          $('#childTicketFareResult').text(data[i]['odpt:childTicketFare']);
          $('#icCardFareResult').text(data[i]['odpt:icCardFare']);
          $('#childIcCardFareResult').text(data[i]['odpt:childIcCardFare']);
        }
      },
      beforeFunc,
      finishFunc
    );
  }
  return {
      initMap: _initMap,
      calculateFare: _calculateFare
  };
})();
