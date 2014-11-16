<?php
namespace Model;

/**
 * 休日保持用モデル<br>
 */
class HolidayModel extends \Model\ModelBase
{
    /**
     * データベースの設定を行う.
     */
    public function setup()
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS holiday (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                year INTEGER,
                month INTEGER,
                day INTEGER,
                type TEXT
            );"
        );
        $this->db->exec(
            "CREATE INDEX IF NOT EXISTS holiday_date_index ON  holiday(year, month, day);"
        );
    }

    /**
     * 指定の年月の休日情報を取得
     * @param  int                   $year  年
     * @param  int                   $month 月
     * @return 日付情報の一覧
     */
    public function getMonthData($year, $month)
    {
        $ret = \ORM::for_table('holiday')
            ->where(
                array(
                    'year' => (int) $year,
                    'month' => (int) $month
                )
            )
            ->order_by_asc('day')
            ->find_array();
        $res = array();
        foreach ($ret as $row) {
            $res += array($row['day'] => $row['type']);
        }

        return $res;
    }

    /**
     * 日付情報を登録
     * @param int $year  年
     * @param int $month 月
     * @data 日付情報の一覧
     */
    public function append($year, $month, $data)
    {
        $this->db->beginTransaction();
        foreach ($data as $day => $type) {
            $row = \ORM::for_table('holiday')->create();
            $row->year = (int) $year;
            $row->month = (int) $month;
            $row->day =  (int) $day;
            $row->type = $type;
            $row->save();
        }
        $this->db->commit();
    }
}
