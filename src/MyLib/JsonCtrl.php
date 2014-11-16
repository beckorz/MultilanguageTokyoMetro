<?php
namespace MyLib;

/**
 */
class JsonCtrl
{
    private $dataFolder;

    /**
     * コンストラクタ
     * @param string $dataFolder JSON情報の格納してあるフォルダ
     */
    public function __construct($dataFolder)
    {
        $this->dataFolder = $dataFolder;
        $this->cache = array();
    }

    public function getRailDirectionType()
    {
        return $this->readTypeJson('getRailDirectionType', $this->dataFolder . '/RailDirectionType.json');
    }

    public function getRailWayType()
    {
        return $this->readTypeJson('getRailWayType', $this->dataFolder . '/RailWayType.json');
    }

    public function getOtherRailWayType()
    {
        return $this->readTypeJson('getOtherRailWayType', $this->dataFolder . '/OtherRailWayType.json');
    }

    public function getTrainOwnerType()
    {
        return $this->readTypeJson('getTrainOwnerType', $this->dataFolder . '/TrainOwnerType.json');
    }

    public function getOperatorType()
    {
        return $this->readTypeJson('getOperatorType', $this->dataFolder . '/OperatorType.json');
    }

    public function getTrainType()
    {
        return $this->readTypeJson('getTrainType', $this->dataFolder . '/TrainType.json');
    }

    public function getTranslationInfo()
    {
        return $this->readTypeJson('getTranslationInfo', $this->dataFolder . '/TranslationInfo.json');
    }

    public function getOtherStationDict()
    {
        return $this->readTypeJson('getOtherStationDict', $this->dataFolder . '/other_stationDict.json');
    }

    public function getFacilityType()
    {
        return $this->readTypeJson('getFacilityType', $this->dataFolder . '/FacilityType.json');
    }

    public function getTrainDirectionConvertTable()
    {
        return $this->readTypeJson(
            'TrainDirectionConvertTable',
            $this->dataFolder . '/TrainDirectionConvertTable.json'
        );
    }

    private function readTypeJson($key, $path)
    {
        if ($this->cache[$key]) {
            return $this->cache[$key];
        }
        $handle = fopen($path, 'r');
        $ret = fread($handle, filesize($path));
        fclose($handle);
        $data = (array) json_decode($ret);
        $this->cache += array($key=>$data);

        return $data;
    }
}
