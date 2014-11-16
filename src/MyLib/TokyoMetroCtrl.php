<?php
namespace MyLib;

/**
 * 東京メトロAPI操作用の継承クラス<br>
 * キャッシュDBにデータが存在すれば、それを取得し、なければAPIを実行する。<br>
 */
class TokyoMetroCtrl extends \MyLib\TokyoMetroApi
{
    private $model;

    /**
     * コンストラクタ
     * @param string   $endPoint    APIのENDPOINT
     * @param string   $consumerKey 東京メトロAPIで登録したComsumerKey
     * @param JsonCtrl $jsonCtrl    JsonCtrl
     * @param \Model\TokyoMetroCacheModel $model       キャッシュを格納するためのモデル
     */
    public function __construct($endPoint, $consumerKey, $jsonCtrl, $model)
    {
        $this->model = $model;
        parent::__construct($endPoint, $consumerKey, $jsonCtrl);
    }

    /**
     * JSONを取得する。
     * @param string $url   対象のURL
     * @param bool   $cache trueの場合はキャッシュからデータを取得する。<BR>
     *                       無事取得できたらキャッシュを登録する.
     * @return array レスポンスの結果
     */
    public function getJson($url, $cache = true)
    {
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($url);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::getJson($url);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($url, $ret);
            }

            return $ret;
        }
    }

    /**
     * 東京メトロ駅情報をすべて取得する
     * @param bool $cache trueの場合はキャッシュからデータを取得する。<BR>
     *                      無事取得できたらキャッシュを登録する.
     * @return array レスポンスの結果
     */
    public function getStations($cache = true)
    {
        $key = 'Station';
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::getStations();
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }
    private function createKeyCond($name, $cond)
    {
        $param = '';
        foreach ($cond as $prop => $val) {
            $param = '_' . $param . $prop . ':' . $val;
        }

        return $name . $param;
    }
    public function findStation($cond, $cache = true)
    {
        $key = $this->createKeyCond('Station', $cond);
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findStation($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    /**
     * 東京メトロ路線情報を全て取得する.
     * @param bool $cache trueの場合はキャッシュからデータを取得する。<BR>
     *                     無事取得できたらキャッシュを登録する.
     * @return array レスポンスの結果
     */
    public function getRailways($cache = true)
    {
        $key = 'Railway';
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::getRailways();
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    public function findRailway($cond, $cache = true)
    {
        $key = $this->createKeyCond('Railway', $cond);
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findRailway($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    /**
     * 地物情報を使用して駅出入り口情報を取得する。
     * @param bool $cache trueの場合はキャッシュからデータを取得する。<BR>
     *                     無事取得できたらキャッシュを登録する.
     * @return array レスポンスの結果
     */
    public function getPois($cache = true)
    {
        $key = 'Poi';
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::getPois();
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    public function findPoi($cond, $cache = true)
    {
        $key = $this->createKeyCond('Poi', $cond);
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findPoi($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    /**
     * 駅の施設に関する情報を取得する。
     * @param bool $cache trueの場合はキャッシュからデータを取得する。<BR>
     *                     無事取得できたらキャッシュを登録する.
     * @return array レスポンスの結果
     */
    public function getStationFacilities($cache = true)
    {
        $key = 'StationFacility';
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::getStationFacilities();
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    public function findStationFacility($cond, $cache = true)
    {
        $key = $this->createKeyCond('StationFacility', $cond);
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findStationFacility($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    /**
     * 駅乗降人員数を取得する。
     * @return array レスポンスの結果
     */
    public function getPassengerSurvey($cache = true)
    {
        $key = 'PassengerSurvey';
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::getPassengerSurvey();
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }
    public function findPassengerSurvey($cond, $cache = true)
    {
        $key = $this->createKeyCond('PassengerSurvey', $cond);
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findPassengerSurvey($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }
    /**
     * 指定のcontents情報からsameAsの内容を抽出する
     * @param  array           $contents API実行時のcontentsの配列
     * @param  string          $sameAs   検索するsameAs
     * @return 抽出した値
     */
    public function findContentBySameAs($contents, $sameAs)
    {
        foreach ($contents as $c) {
            if ($c->{'owl:sameAs'} === $sameAs) {
                return $c;
            }
        }

        return null;
    }

    /**
     * 駅の配列の一覧から指定のsameAsのタイトルを取得する
     * @param  string $stations 東京メトロの駅の配列
     * @param  string $sameAs   sameAsの値
     * @return string 駅名。東京メトロ外の場合はJSONファイルから取得
     */
    public function findStationTitle($stations, $sameAs)
    {
        $s = $this->findContentBySameAs($stations, $sameAs);
        if ($s) {
            return $s->{'dc:title'};
        }
        $otherJson = $this->getOtherStationDict();

        return $otherJson[$sameAs];
    }

    /**
     * 運賃を計算する
     */
    public function findFare($cond, $cache = true)
    {
        $key = $this->createKeyCond('Fare', $cond);
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findFare($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    public function findFareByFromTo($from, $to, $cache = true)
    {
        $param = array('odpt:fromStation'=>$from,
                       'odpt:toStation'=>$to);

        return $this->findFare($param);
    }

    /**
     * 列車時刻表情報 の取得
     */
    public function findTrainTimetable($cond, $cache = true)
    {
        $key = $this->createKeyCond('TrainTimetable', $cond);
        $ret = null;
        if ($cache) {
            $ret = $this->model->getContents($key);
        }
        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findTrainTimetable($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    /**
     * 駅時刻表情報 の取得
     */
    public function findStationTimetable($cond, $cache = true)
    {
        $key = $this->createKeyCond('StationTimetable', $cond);
        $ret = null;

        if ($cache) {
            $ret = $this->model->getContents($key);
        }

        if ($ret) {
            return $ret;
        } else {
            $ret = parent::findStationTimetable($cond);
            $ret += array('updated'=>time());
            if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                $this->setTrainNoToStationTimetable($ret);
                $this->model->setContents($key, $ret);
            }

            return $ret;
        }
    }

    /**
     * 駅時刻表に列車時刻表の列車情報を紐づける
     */
    private function setTrainNoToStationTimetable(&$stationTimetableRet)
    {
        if ($stationTimetableRet['trainNoUpdated']) {
            return;
        }
        if ($stationTimetableRet['resultCode'] !== \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            return;
        }
        $contents = $stationTimetableRet['contents'];
        foreach ($contents as $c) {
            $terminals = array();
            foreach ($c->{'odpt:weekdays'} as $row) {
                if (
                    !in_array(
                        $row->{'odpt:destinationStation'},
                        $terminals
                    )
                ) {
                    array_push($terminals, $row->{'odpt:destinationStation'});
                }
            }

            foreach ($c->{'odpt:saturdays'} as $row) {
                if (
                    !in_array(
                        $row->{'odpt:destinationStation'},
                        $terminals
                    )
                ) {
                    array_push($terminals, $row->{'odpt:destinationStation'});
                }
            }

            foreach ($c->{'odpt:holidays'} as $row) {
                if (
                    !in_array(
                        $row->{'odpt:destinationStation'},
                        $terminals
                    )
                ) {
                    array_push($terminals, $row->{'odpt:destinationStation'});
                }
            }

            // APIの列車情報不具合のせいで、いくつかの駅が取れないので強引に追記
            if (in_array('odpt.Station:TokyoMetro.Yurakucho.Wakoshi', $terminals)) {
                if (!in_array('odpt.Station:TokyoMetro.Fukutoshin.Wakoshi', $terminals)) {
                    array_push($terminals, 'odpt.Station:TokyoMetro.Fukutoshin.Wakoshi');
                }
            }
            if (in_array('odpt.Station:TokyoMetro.Marunouchi.NakanoSakaue', $terminals)) {
                if (!in_array('odpt.Station:TokyoMetro.MarunouchiBranch.NakanoSakaue', $terminals)) {
                    array_push($terminals, 'odpt.Station:TokyoMetro.MarunouchiBranch.NakanoSakaue');
                }
            }
            
            foreach ($terminals as $terminal) {
                $trainTimetable = $this->findTrainTimetable(array('odpt:terminalStation'=>$terminal));
                if ($ret['resultCode'] === \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                    return;
                }

                $trainTmContetns = $trainTimetable['contents'];
                if ($c->{'odpt:weekdays'}) {
                    $this->setTrainNoToTimetable(
                        $c->{'odpt:station'},
                        $c->{'odpt:railway'},
                        $c->{'odpt:weekdays'},
                        $trainTmContetns,
                        'odpt:weekdays'
                    );
                }
                if ($c->{'odpt:saturdays'}) {
                    $this->setTrainNoToTimetable(
                        $c->{'odpt:station'},
                        $c->{'odpt:railway'},
                        $c->{'odpt:saturdays'},
                        $trainTmContetns,
                        'odpt:saturdays'
                    );
                }
                if ($c->{'odpt:holidays'}) {
                    $this->setTrainNoToTimetable(
                        $c->{'odpt:station'},
                        $c->{'odpt:railway'},
                        $c->{'odpt:holidays'},
                        $trainTmContetns,
                        'odpt:holidays'
                    );
                }
            }
        }
        $stationTimetableRet += array('trainNoUpdated'=>time());

        return $stationTimetableRet;
    }

    private function setTrainNoToTimetable($stationName, $railway, &$table, &$trainTmContetns, $propName)
    {
        foreach ($table as $row) {
            $setFlag = false;
            foreach ($trainTmContetns as $tm) {
                // 停車駅だと準急だろうが、特急だろうが各駅停車となる。
                // https://developer.tokyometroapp.jp/forum/forums/1/topics/odpt-traintype-tokyometro-rapid
                //if ($tm->{'odpt:trainType'} !== $row->{'odpt:trainType'}) {
                //    // 列車タイプが違うので該当データではない
                //    continue;
                //}
                if (!$tm->{$propName}) {
                    // 曜日が違うので該当データではない
                    continue;
                }

                $trainTbl = $tm->{$propName};
                if ($this->compTime($row->{'odpt:departureTime'}, $trainTbl[0]->{'odpt:departureTime'}) < 0) {
                    // 列車時刻表の始発の発射時間より小さい場合は対象外
                    continue;
                }
                if (
                    $this->compTime(
                        $row->{'odpt:departureTime'},
                        $trainTbl[count($trainTbl)-2]->{'odpt:departureTime'}
                    ) > 0) {
                    // 列車時刻表の終着駅の一つ前の出発時刻より大きい場合は対象外
                    continue;
                }
                foreach ($trainTbl as $tRow) {
                    if (
                        (
                            $row->{'odpt:departureTime'} == $tRow->{'odpt:departureTime'}
                        ) && $this->chkSameStation(
                            $stationName,
                            $row->{'odpt:destinationStation'},
                            $tRow->{'odpt:departureStation'},
                            $tm->{'odpt:terminalStation'}
                        )
                    ) {
                        $row->{'odpt:trainNumber'} = $tm->{'odpt:trainNumber'};
                        $row->{'odpt:train'} = $tm->{'odpt:train'};
                        $row->{'odpt:TrainTimetable'} = $tm->{'owl:sameAs'};
                        $setFlag = true;
                        break;
                    }
                }
                if ($setFlag) {
                    break;
                }
            }
        }
    }

    /**
     * 時刻を比較する<BR>
     * 00時以降は02時までは翌日とみなし、23時より大きいものとする.
     */
    public function compTime($a, $b)
    {
        $aHour = explode(':', $a);
        if ($aHour[0] == '00' ||
            $aHour[0] == '01' ||
            $aHour[0] == '02') {
            $a = 'N' . $a;
        }
        $bHour = explode(':', $b);
        if ($bHour[0] == '00' ||
            $bHour[0] == '01' ||
            $bHour[0] == '02') {
            $b = 'N' . $b;
        }

        return strcmp($a, $b);
    }
    private function chkSameStation($a,$destinationStation, $b, $terminalStation)
    {
        if ($a === $b) {
            return true;
        }
        $apath = explode('.', $a);
        $bpath = explode('.', $b);
        if ($apath[count($apath)-1] == $bpath[count($bpath)-1]) {
            $dpath = explode('.', $destinationStation);
            $tpath = explode('.', $terminalStation);
            if ($dpath[count($dpath)-1] == $tpath[count($tpath)-1]) {
                return true;
            }
        }
        return false;
    }
}
