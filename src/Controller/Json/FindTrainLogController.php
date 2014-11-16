<?php
namespace Controller\Json;

/**
 * 指定の更新日の列車ロケーション情報を取得する
 * updateパラメータにて更新日の指定ができるものとする.
 */
class FindTrainLogController extends \Controller\ControllerBase
{
    public function route()
    {
        $model = $this->models['TrainLogModel'];
        $tran = $this->modules['MsTranslator'];

        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $trainTypes = $tmCtrl->getTrainType();

        $updated = time();
        if ($this->app->request->params('updated')) {
            $updated = strtotime($this->app->request->params('updated'));
        }
        $rail_direction = $tmCtrl->getRailDirectionType();
        $trainTypes = $tmCtrl->getTrainType();
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
        $railways = $tmCtrl->getRailWayType();

        $res = array();

        $ret = $model->findLog($updated);
        foreach ($ret as $key => $row) {
            $traintype = $trainTypes[$row{'train_type'}];

            $info = array('trainNo'=> $row['train_number'],
                          'sameAs' => $row['same_as'],
                          'trainUrl' => $this->createPageUrl('train_timetable', 'train='.$row['same_as']) ,
                          'fromStation' => self::getName($row['from_station']),
                          'fromStationTitle' =>  $tmCtrl->findStationTitle($stations, $row['from_station']),
                          'toStation' => self::getName($row['to_station']),
                          'toStationTitle' =>  $tmCtrl->findStationTitle($stations, $row['to_station']),
                          'startingStation' => self::getName($row['starting_station']),
                          'startingStationTitle'=> $tmCtrl->findStationTitle($stations, $row['starting_station']) ,
                          'terminalStation' => self::getName($row['terminal_station']),
                          'terminalStationTitle' => $tmCtrl->findStationTitle($stations, $row['terminal_station']),
                          'delay' => $row['delay'],
                          'railway' => $railways[$row['railway']]->title,
                          'railwayUrl' => $this->createPageUrl('railway_info', 'railway=' . $row['railway']),
                          'direction' => $rail_direction[$row['rail_direction']],
                          'trainType' => $traintype,
                          'valid' => date("Y/m/d H:i:s", $row['valid']),
                          'created' =>  date("Y/m/d H:i:s", $row['created'])
                    );
            array_push($res, $info);
        }
        $this->sendJsonData(\MyLib\TokyoMetroApi::RESULT_CODE_OK, null, $res);

        return;
    }
}
