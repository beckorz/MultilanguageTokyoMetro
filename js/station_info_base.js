/**
 * 駅情報画面の共通処理のスクリプト
 */
var stationInfoBase = (function() {
  function createGoogleLatLng(latJ, lonJ) {
    return new google.maps.LatLng(latJ, lonJ);
  }
  var map = null;
  var panorama;
  var infowindow = null;

  /**
   * 出口検索用のGoogleマップの初期化
   */
  function _initMap() {
    var stationLoc = $('#stationLoc');
    var latlng = new google.maps.LatLng(stationLoc.attr('lat'), stationLoc.attr('long'));
    var opts = {
      zoom: 18,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map_canvas'), opts);
    // 乗り換え案内レイヤ
    var transitLayer = new google.maps.TransitLayer();
    transitLayer.setMap(map);

    var panoramaOptions = {
      position: latlng,
      pov: {
        heading: 34,
        pitch: 10
      }
    };
    panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);
    map.setStreetView(panorama);
  }

  /**
   * 出口検索
   */
  function _changeExit(lat, long, value) {
    if (infowindow) {
      infowindow.close();
    }
    latlng = createGoogleLatLng(lat, long);
    map.panTo(latlng);

    if (panorama) {
      // 四谷駅のように一旦ストリートビューが解除されるので一旦削除して作り直し
      map.setStreetView(null);
      panorama = null;
      var panoramaOptions = {
        position: latlng,
        pov: {
          heading: 34,
          pitch: 10
        }
      };
      panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);
      map.setStreetView(panorama);
    }

    infowindow = new google.maps.InfoWindow({
      content: '<P>' + value + '</P>',
      position: latlng
    });
    infowindow.setMap(map);
  }

  /**
   * 出口検索用Googleマップの更新
   */
  function _refreshMap() {
    if (map) {
        google.maps.event.trigger(map, 'resize');
    }
  }

  return {
      initMap: _initMap,
      changeExit: _changeExit,
      refreshMap: _refreshMap
  };
})();
