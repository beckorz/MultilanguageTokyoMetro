<?php
namespace MyLib;

/**
 * @class HolidyCtrl
 * @brief 休日情報の操作
 */
class HolidayCtrl
{
    const SERVICE_URL = 'http://www.finds.jp/ws/calendar.php';
    private $model;

    /**
     * コンストラクタ
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    private function getStationJson($url)
    {
        $json = file_get_contents($url);

        return json_decode($json);
    }

    /**
     * 指定の日付タイプを取得する
     * @param int 検査対象の時刻のタイムスタンプ
     * @return string 'weekdays'/'saturdays'/'holidays'
     */
    public function checkDayType($t)
    {
        $y = date('Y', $t);
        $m = date('m', $t);
        $d = date('d', $t);
        $ret = $this->model->getMonthData($y, $m);
        if (count($ret) > 0) {
            // キャッシュが存在する場合
            if ($ret[(int) $d]) {
                return $ret[(int) $d];
            } else {
                return 'weekdays';
            }
        }
        $res = $this->doMonthHolidayApi($y, $m);
        if ($res) {
            // API が正常に動作した場合は、DBにキャッシュを登録してデータを返す
            $this->model->append($y, $m, $res);
            if ($res[(int) $d]) {
                return $res[(int) $d];
            } else {
                return 'weekdays';
            }
        } else {
            // API が動作しない場合は、曜日だけみて返す
            $w = (int) date('w', $t);
            if ($w ==0) {
                return 'holidays';
            } elseif ($w ==6) {
                return 'saturdays';
            } else {
                return 'weekdays';
            }
        }
    }

    public function doMonthHolidayApi($y, $m)
    {
        $url = self::SERVICE_URL . '?json&y='. $y . '&m='. $m . '&l=3';
        $json = $this->getStationJson($url);
        if (!$json) {
            return null;
        }
        if ($json->status != 200) {
            return null;
        }
        $res = array();
        foreach ($json->result->day as $day) {
            $type = 'weekdays';
            if ($day->htype === 1 ||
                $day->htype === 2 ||
                $day->htype === 3) {
                $type = 'holidays';
            } elseif ($day->wday === 1) {
                $type = 'holidays';
            } elseif ($day->wday === 7) {
                $type = 'saturdays';
            }
            if ($type != 'weekdays') {
                $res += array($day->mday => $type);
            }
        }

        return $res;
    }
}
