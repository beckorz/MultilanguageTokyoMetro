<?php
namespace MyLib;

/**
 * 東京メトロAPI実行用のクラス<br>
 * 各APIのレスポンスは以下の仕様に準拠している。<br>
 * @link https://developer.tokyometroapp.jp/documents/railway 駅情報ボキャブラリ仕様書
 * @link https://developer.tokyometroapp.jp/documents/facility 地物情報ボキャブラリ仕様書
 */
class TokyoMetroApi
{
    private $client;
    private $consumerKey;
    private $jsonCtrl;
    private $endPoint;

    /** 結果コード：正常終了 */
    const RESULT_CODE_OK = 0;

    /** 結果コード：東京メトロAPIの異常 */
    const RESULT_CODE_ERR_API = 1;

    /** 東京メトロAPIの最大リトライ数 */
    const MAX_TRY_COUNT = 3;

    /** 東京メトロAPIのリトライ感覚 */
    const WAIT_COUNT = 100000; // 1ms

    /**
     * コンストラクタ
     * @param string   $endPoint    APIのENDPOINT
     * @param string   $consumerKey 東京メトロAPIで登録したComsumerKey
     * @param JsonCtrl $jsonCtrl    JsonCtrl
     */
    public function __construct($endPoint, $consumerKey, $jsonCtrl)
    {
        $this->endPoint = $endPoint;
        $this->consumerKey = $consumerKey;
        $this->client = new \HTTP_Client();
        $this->jsonCtrl = $jsonCtrl;
    }

    public function getRailDirectionType()
    {
        return $this->jsonCtrl->getRailDirectionType();
    }
    public function getRailWayType()
    {
        return $this->jsonCtrl->getRailWayType();
    }
    public function getOtherRailWayType()
    {
        return $this->jsonCtrl->getOtherRailWayType();
    }
    public function getTrainOwnerType()
    {
        return $this->jsonCtrl->getTrainOwnerType();
    }
    public function getOperatorType()
    {
        return $this->jsonCtrl->getOperatorType();
    }
    public function getTrainType()
    {
        return $this->jsonCtrl->getTrainType();
    }
    public function getOtherStationDict()
    {
        return $this->jsonCtrl->getOtherStationDict();
    }
    public function getFacilityType()
    {
        return $this->jsonCtrl->getFacilityType();
    }

    private function readTypeJson($path)
    {
        $handle = fopen($path, 'r');
        $ret = fread($handle, filesize($path));
        fclose($handle);

        return (array) json_decode($ret);
    }

    /**
     * APIを実行してレスポンスの取得
     * もし、503エラーの場合は、WAIT_COUNTマイクロ秒後に
     * MAX_TRY_COUNT回までリトライする。
     * @param  string $url      対象のURL
     * @param  array  $param    パラメータの連想配列
     * @param  int    $trycount 現在の試行回数
     * @return array  レスポンスの結果
     */
    private function getResponse($url, $param, $trycount)
    {
        $code = $this->client->get($url, $param);
        $res = $this->client->currentResponse();
        $body = null;
        if ($res) {
            if (isset($res['body'])) {
                $body = $res['body'];
            }
        }
        $ret = null;
        if ($code != 200) {
            // リトライ処理.
            // 100ms程度でブロックが掛かっているので,スリープして再実行.
            // https://developer.tokyometroapp.jp/forum/forums/1/topics/http-request-failed-http-1-1-503-service-unavailable
            if ($code == 503 and $trycount < TokyoMetroApi::MAX_TRY_COUNT) {
                // for memory leak
                $this->client->reset();

                usleep(($trycount + 1) * TokyoMetroApi::WAIT_COUNT);

                return $this->getResponse($url, $param, $trycount + 1);
            }

            $msg = sprintf('ResponceCode: %d Message:%s', $code, $body);
            $ret = array('resultCode' => TokyoMetroApi::RESULT_CODE_ERR_API,
                                     'errorMsg' => $msg,
                                     'contents' => null);
        } else {
            $ret = array('resultCode' => TokyoMetroApi::RESULT_CODE_OK,
                                     'errorMsg' => null,
                                     'contents' => json_decode($body));
        }

        // for memory leak
        $this->client->reset();

        return $ret;
    }

    /**
     * JSONを取得する。
     * @param  string $url 対象のURL
     * @return array  レスポンスの結果
     */
    public function getJson($url)
    {
        $param = array('acl:consumerKey' => $this->consumerKey);

        return $this->getResponse($url, $param, 0);
    }

    /**
     * データポイント用のAPIを実行する
     * @param  array $param パラメータの連想配列
     * @return array レスポンスの結果
     */
    private function getDataPoints($param)
    {
        $url = $this->endPoint . 'datapoints';

        return $this->getResponse($url, $param, 0);
    }

    private function getPlaces($param)
    {
        $url = $this->endPoint . 'places';

        return $this->getResponse($url, $param, 0);
    }

    /**
     * 東京メトロ駅情報をすべて取得する
     * @return array レスポンスの結果
     */
    public function getStations()
    {
        $param = array('rdf:type' => 'odpt:Station',
                       'acl:consumerKey' => $this->consumerKey);

        return $this->getDataPoints($param);
    }

    /**
     * 東京メトロ駅情報を指定の条件で検索する.
     * <code>
     * $jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);
     * $ctrl = new TokyoMetroApi(CONSUMER_KEY,$jsonCtrl);
     * $param=array('odpt:railway' => 'odpt.Railway:TokyoMetro.Marunouchi');
     * $ret = $ctrl->findStation($param);
     * </code>
     * @param  array $conditions 条件を指定した連想配列
     * @return array レスポンスの結果
     */
    public function findStation($conditions)
    {
        $param = array('rdf:type' => 'odpt:Station',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }

    /**
     * 東京メトロ駅情報を位置情報の条件で検索する.
     * <code>
     * $jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);
     * $ctrl = new TokyoMetroApi(CONSUMER_KEY,$jsonCtrl);
     * $param=array('lat' => 35.6729562407498,
     *              'lon' =>139.724074594678,
     *              'radius' => 100);
     * $ret = $ctrl->findStation($param);
     * </code>
     * @param  array $conditions 条件を指定した連想配列
     * @return array レスポンスの結果
     */
    public function findStationByPlace($conditions)
    {
        $param = array('rdf:type' => 'odpt:Station',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getPlaces($param);
    }
    /**
     * 東京メトロ路線情報を全て取得する.
     * @return array レスポンスの結果
     */
    public function getRailways()
    {
        $param = array('rdf:type' => 'odpt:Railway',
                       'acl:consumerKey' => $this->consumerKey);

        return $this->getDataPoints($param);
    }
    /**
     * 東京メトロ路線情報を指定の条件で検索する.
     * <code>
     * $jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);
     * $ctrl = new TokyoMetroApi(CONSUMER_KEY,$jsonCtrl);
     * $param=array('owl:sameAs' => 'odpt.Railway:TokyoMetro.Marunouchi');
     * $ret = $ctrl->findRailway($param);
     * </code>
     * @param  array $conditions 条件を指定した連想配列
     * @return array レスポンスの結果
     */
    public function findRailway($conditions)
    {
        $param = array('rdf:type' => 'odpt:Railway',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }
    public function findRailwayByPlace($conditions)
    {
        $param = array('rdf:type' => 'odpt:Railway',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getPlaces($param);
    }
    /**
     * 地物情報を使用して駅出入り口情報を取得する。
     * @return array レスポンスの結果
     */
    public function getPois()
    {
        $param = array('rdf:type' => 'ug:Poi',
                       'acl:consumerKey' => $this->consumerKey);

        return $this->getDataPoints($param);
    }

    /**
     * 地物情報を使用して駅出入り口情報を取得する。
     * <code>
     * $jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);
     * $ctrl = new TokyoMetroApi(CONSUMER_KEY,$jsonCtrl);
     * $ret = $ctrl->findPoi(array('@id'=>'urn:ucode:_00001C000000000000010000030C3EC1'));
     * if ($ret['resultCode'] != TokyoMetroApi::RESULT_CODE_OK) {
     *   sendJsonData($ret['resultCode'],  $ret['errorMsg'], null);
     *   exit();
     * }
     * $exit_info = $ret['contents'][0];
     * </code>
     * @param  array $conditions 条件を指定した連想配列
     * @return array レスポンスの結果
     */
    public function findPoi($conditions)
    {
        $param = array('rdf:type' => 'ug:Poi',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }
    public function findPoiByPlace($conditions)
    {
        $param = array('rdf:type' => 'ug:Poi',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getPlaces($param);
    }

    /**
     * 運賃を取得する
     * @param $coditions 検索条件
     * @return array レスポンスの結果
     */
    public function findFare($conditions)
    {
        $param = array('rdf:type' => 'odpt:RailwayFare',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }

    /**
     * 駅の施設に関する情報を取得する。
     * @return array レスポンスの結果
     */
    public function getStationFacilities()
    {
        $param = array('rdf:type' => 'odpt:StationFacility',
                       'acl:consumerKey' => $this->consumerKey);

        return $this->getDataPoints($param);
    }
    public function findStationFacility($conditions)
    {
        $param = array('rdf:type' => 'odpt:StationFacility',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }

    /**
     *  列車運行情報 の取得
     * @return array レスポンスの結果
     */
    public function findTrainInformation($conditions)
    {
        $param = array('rdf:type' => 'odpt:TrainInformation',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }
    /**
     *  列車ロケーション情報 の取得
     * @return array レスポンスの結果
     */
    public function findTrain($conditions)
    {
        $param = array('rdf:type' => 'odpt:Train',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }

    /**
     * 駅乗降人員数を取得する。
     * @return array レスポンスの結果
     */
    public function getPassengerSurvey()
    {
        $param = array('rdf:type' => 'odpt:PassengerSurvey',
                       'acl:consumerKey' => $this->consumerKey);

        return $this->getDataPoints($param);
    }
    public function findPassengerSurvey($conditions)
    {
        $param = array('rdf:type' => 'odpt:PassengerSurvey',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;
        return $this->getDataPoints($param);
    }

    /**
     *  列車時刻表情報 の取得
     * @return array レスポンスの結果
     */
    public function findTrainTimetable($conditions)
    {
        $param = array('rdf:type' => 'odpt:TrainTimetable',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }
    /**
     * 駅時刻表情報 の取得
     * @return array レスポンスの結果
     */
    public function findStationTimetable($conditions)
    {
        $param = array('rdf:type' => 'odpt:StationTimetable',
                       'acl:consumerKey' => $this->consumerKey);
        $param += $conditions;

        return $this->getDataPoints($param);
    }
}
