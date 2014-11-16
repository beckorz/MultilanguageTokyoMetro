<?php
namespace Controller\Json;

/**
 * 位置情報から駅の情報を返す.<BR>
 */
class GetStationTimetableController extends \Controller\ControllerBase
{
    const LIMIT_RESULT = 10;
    public function route()
    {
        $station = $this->app->request->params('station');
        if (!$station) {
            $this->app->halt(500, 'station is not defined.');
            return;
        }
        $direction = $this->app->request->params('direction');
        if (!$direction) {
            $this->app->halt(500, 'direction is not defined.');
            return;
        }
        $date = $this->app->request->params('date');
        if (!$direction) {
            $this->app->halt(500, 'date is not defined.');
            return;
        }

        $tran = $this->modules['MsTranslator'];
        $holidayCtrl = $this->modules['HolidayCtrl'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        // 駅情報取得
        $ret = $tmCtrl->getStations();
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);
            return;
        }
        $stations = $ret['contents'];
        if (!$stations) {
            $this->app->halt(500, 'internal error.');
            return;
        }

        // 列車タイムテーブル
        $ret = $tmCtrl->findStationTimetable(
            array(
                'odpt:station' => $station,
                'odpt:railDirection'=>$direction
            )
        );
        if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);
            return;
        }
        $stationTimetable = $ret['contents'][0];
        $daytype = $holidayCtrl->checkDayType($date);
        if (!$stationTimetable->{'odpt:' . $daytype}) {
            $this->app->halt(500, 'day type is not found.');
            return;
        }
        $curDateStr = date("H:i", $date);
        $timetable = $stationTimetable->{'odpt:' . $daytype};
        $res = array();
        $cnt = 0;
        foreach ($timetable as $row) {
            if ($cnt >= GetStationTimetableController::LIMIT_RESULT) {
                break;
            }
            if ($tmCtrl->compTime($curDateStr, $row->{'odpt:departureTime'}) <= 0) {
                $data = $this->createData($row, $tmCtrl, $trainTypes, $stations);
                array_push($res, $data);
                $cnt += 1;
            }
        }

        // 件数に満たない場合は件数追加
        foreach ($timetable as $row) {
            if ($cnt >= GetStationTimetableController::LIMIT_RESULT) {
                break;
            }
            $data = $this->createData($row, $tmCtrl, $trainTypes, $stations);
            array_push($res, $data);
            $cnt += 1;
        }
        $this->sendJsonData(\MyLib\TokyoMetroApi::RESULT_CODE_OK, null, $res);
    }

    private function createData($row, $tmCtrl, $trainTypes, $stations)
    {
        $ds = $tmCtrl->findContentBySameAs($stations, $row->{'odpt:destinationStation'});
        $dt = $tmCtrl->findStationTitle($stations, $row->{'odpt:destinationStation'});
        $durl = null;
        if ($ds) {
            $durl= $this->createPageUrl('station_info', 'station=' . $ds->{'owl:sameAs'});
        }
        $data =array(
            'departureTime' => $row->{'odpt:departureTime'},
            'trainType' => $trainTypes[$row->{'odpt:trainType'}],
            'destinationStation' => $row->{'odpt:destinationStation'},
            'destinationStationTitle' => $dt,
            'destinationStationUrl' => $durl,
            'trainNumber' => $row->{'odpt:trainNumber'},
            'train' => $row->{'odpt:train'},
            'trainUrl' => $this->createPageUrl('train_timetable', 'train=' . $row->{'odpt:train'}),
            'carComposition' => $row->{'odpt:carComposition'},
            'note' => $row->{'odpt:note'},
            'isOrigin' => $row->{'odpt:isOrigin'},
            'isLast' => $row->{'odpt:isLast'}
        );
        return $data;
    }
}
