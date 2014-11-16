<?php
namespace MyLib;

/**
 * 東京メトロAPI操作用の多言語対応クラス<br>
 * キャッシュDBにデータが存在すれば、それを取得し、なければAPIを実行する。<br>
 */
class TokyoMetroMultiLangCtrl extends \MyLib\TokyoMetroCtrl
{
    private $tran;

    /**
     * コンストラクタ
     * @param string   $endPoint    APIのENDPOINT
     * @param string   $consumerKey 東京メトロAPIで登録したComsumerKey
     * @param JsonCtrl $jsonCtrl    JsonCtrl
     * @param \Model\TokyoMetroCacheModel $model       キャッシュを格納するためのモデル
     * @param \MyLib\MsTranslator         $tran        翻訳用クラス
     */
    public function __construct($endPoint, $consumerKey, $jsonCtrl, $model, $tran)
    {
        $this->tran = $tran;
        parent::__construct($endPoint, $consumerKey, $jsonCtrl, $model);
    }

    private function translateHashValue($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = $this->tran->translator($value);
        }

        return $data;
    }
    private function translatePropertiesBase($contents, $props)
    {
        foreach ($contents as $c) {
            foreach ($props as $p) {
                if (!property_exists($c, $p)) {
                    continue;
                }
                if (is_array($c->{$p})) {
                    for ($i=0; $i<count($c->{$p}); $i++) {
                        $c->{$p}[$i] = $this->tran->translator($c->{$p}[$i]);
                    }
                } else {
                    $c->{$p} = $this->tran->translator($c->{$p});
                }
            }
        }

        return $contents;
    }
    private function translateProperties($data, $props)
    {
        if ($data['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            return $data;
        }
        $data['contents'] = $this->translatePropertiesBase($data['contents'], $props);

        return $data;
    }

    private function translateOptionalProp($obj, $prop)
    {
        if (property_exists($obj, $prop)) {
            $obj->{$prop} = $this->tran->translator($obj->{$prop});
        }
    }

    private function translateFacility($data)
    {
        if ($data['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            return $data;
        }
        $contents = $data['contents'];
        foreach ($contents as $c) {
            if (property_exists($c, 'odpt:barrierfreeFacility')) {
                foreach ($c->{'odpt:barrierfreeFacility'} as $b) {
                    if (!$b) {
                        continue;
                    }
                    $this->translateOptionalProp($b, 'ugsrv:categoryName');
                    $this->translateOptionalProp($b, 'odpt:placeName');
                    $this->translateOptionalProp($b, 'odpt:locatedAreaName');
                    $this->translateOptionalProp($b, 'ugsrv:remark');
                    if (property_exists($b, 'odpt:serviceDetail')) {
                        foreach ($b->{'odpt:serviceDetail'} as $d) {
                            $this->translateOptionalProp($d, 'odpt:operationDay');
                            $this->translateOptionalProp($d, 'ug:direction');
                            if (!preg_match("/^[a-zA-Z0-9\:]+$/", $c->{'ugsrv:serviceStartTime'})) {
                                $this->translateOptionalProp($d, 'ugsrv:serviceStartTime');
                            }
                            if (!preg_match("/^[a-zA-Z0-9\:]+$/", $c->{'ugsrv:serviceEndTime'})) {
                                $this->translateOptionalProp($d, 'ugsrv:serviceEndTime');
                            }
                        }
                    }
                }
            }
            if (property_exists($c, 'odpt:platformInformation')) {
                foreach ($c->{'odpt:platformInformation'} as $p) {
                    if (property_exists($p, 'odpt:surroundingArea')) {
                        foreach ($p->{'odpt:surroundingArea'} as $key => $value) {
                            $p->{'odpt:surroundingArea'}[$key] = $this->tran->translator($value);
                        }
                    }
                }
            }
        }

        return $data;
    }
    public function getRailDirectionType()
    {
        $data = parent::getRailDirectionType();

        return $this->translateHashValue($data);
    }
    public function getRailWayType()
    {
        $data = parent::getRailWayType();

        return $this->translatePropertiesBase($data, array('title'));
    }
    public function getOtherRailWayType()
    {
        $data = parent::getOtherRailWayType();

        return $this->translatePropertiesBase($data, array('title'));
    }
    public function getTrainOwnerType()
    {
        $data = parent::getTrainOwnerType();

        return $this->translateHashValue($data);
    }
    public function getOperatorType()
    {
        $data = parent::getOperatorType();

        return $this->translateHashValue($data);
    }

    public function getTrainType()
    {
        $data = parent::getTrainType();

        return $this->translateHashValue($data);
    }
    public function getOtherStationDict()
    {
        $data = parent::getOtherStationDict();

        return $this->translateHashValue($data);
    }
    public function getFacilityType()
    {
        $data = parent::getFacilityType();

        return $this->translatePropertiesBase($data, array('title'));
    }

    public function getStations($cache = true)
    {
        $data = parent::getStations($cache);

        return $this->translateProperties($data, array('dc:title'));
    }
    public function findStation($conditions)
    {
        $data = parent::findStation($conditions);

        return $this->translateProperties($data, array('dc:title'));
    }
    public function findStationByPlace($conditions)
    {
        $data = parent::findStationByPlace($conditions);

        return $this->translateProperties($data, array('dc:title'));
    }
    public function getRailways($cache = true)
    {
        $data = parent::getRailways($cache);

        return $this->translateProperties($data, array('dc:title'));
    }
    public function findRailway($conditions)
    {
        $data = parent::findRailway($conditions);

        return $this->translateProperties($data, array('dc:title'));
    }
    public function findRailwayByPlace($conditions)
    {
        $data = parent::findRailwayByPlace($conditions);

        return $this->translateProperties($data, array('dc:title'));
    }

    public function getPois($cache = true)
    {
        $data = parent::getPois($cache);

        return $this->translateProperties($data, array('dc:title','ugsrv:categoryName'));
    }

    public function findPoi($conditions)
    {
        $data = parent::findPoi($conditions);

        return $this->translateProperties($data, array('dc:title','ugsrv:categoryName'));
    }

    public function findPoiByPlace($conditions)
    {
        $data = parent::findPoiByPlace($conditions);

        return $this->translateProperties($data, array('dc:title','ugsrv:categoryName'));
    }

    public function getStationFacilities($cache = true)
    {
        $data = parent::getStationFacilities($cache);

        return $this->translateFacility($data);
    }

    public function findStationFacility($conditions)
    {
        $data = parent::findStationFacility($conditions);

        return $this->translateFacility($data);
    }
    public function findTrainInformation($conditions)
    {
        $data = parent::findTrainInformation($conditions);

        return $this->translateProperties($data, array('odpt:trainInformationText', 'odpt:trainInformationStatus'));
    }
    public function findStationTimetable($conditions)
    {
        $data = parent::findStationTimetable($conditions);

        return $this->translateStationTimetable($data);
    }
    private function translateStationTimetable($data)
    {
        if ($data['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            return $data;
        }
        $contents = $data['contents'];
        foreach ($contents as $c) {
            if (property_exists($c, 'odpt:weekdays')) {
                foreach ($c->{'odpt:weekdays'} as $row) {
                    if (!$row) {
                        continue;
                    }
                    $this->translateOptionalProp($row, 'odpt:note');
                }
            }
            if (property_exists($c, 'odpt:holidays')) {
                foreach ($c->{'odpt:holidays'} as $row) {
                    if (!$row) {
                        continue;
                    }
                    $this->translateOptionalProp($row, 'odpt:note');
                }
            }
            if (property_exists($c, 'odpt:saturdays')) {
                foreach ($c->{'odpt:saturdays'} as $row) {
                    if (!$row) {
                        continue;
                    }
                    $this->translateOptionalProp($row, 'odpt:note');
                }
            }
        }

        return $data;
    }
}
