<?php
namespace Model;

/**
 * 東京メトロAPIの列車ロケーション情報を記録するモデル<br>
 */
class TrainLogModel extends \Model\ModelBase
{
    /**
     * データベースの設定を行う.
     */
    public function setup()
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS train_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ucode TEXT,
                railway TEXT,
                same_as TEXT,
                train_number TEXT,
                train_type TEXT,
                delay INTEGER,
                starting_station TEXT,
                terminal_station TEXT,
                from_station TEXT,
                to_station TEXT,
                rail_direction TEXT,
                owner TEXT,
                created TIMESTAMP,
                valid TIMESTAMP,
                updated TIMESTAMP
            );"
        );
        $this->db->exec(
            "CREATE INDEX IF NOT EXISTS train_log_railway_index ON  train_log(railway);"
        );
        $this->db->exec(
            "CREATE INDEX IF NOT EXISTS train_log_created_index ON  train_log(created);"
        );
        $this->db->exec(
            "CREATE INDEX IF NOT EXISTS train_log_updated_index ON  train_log(updated);"
        );
    }

    /**
     * 特定の路線の列車ロケーション情報のログを新しいもの順で取得する
     * @param  string $railway 路線名
     * @param  string $limit   取得件数
     * @param  string $offset  取得開始位置
     * @return .
     */
    public function getLogs($id, $railway, $limit, $offset)
    {
        $cond = \ORM::for_table('train_log');
        if ($railway) {
            $cond = $cond->where_equal('railway', $railway);
        }
        if ($id) {
            $cond = $cond->where_equal('id', $id);
        }
        $ret = $cond->limit($limit)
            ->offset($offset)
            ->order_by_desc('created')
            ->find_array();

        return $ret;
    }
    /**
     * 特定の路線の列車ロケーション情報のログを新しいもの順で取得する
     * @return .
     */
    public function findLog($created)
    {
        $cond = \ORM::for_table('train_log');
        $cond = $cond->where_equal('updated', $created);
        $ret = $cond->order_by_desc('updated')
            ->find_array();

        return $ret;
    }
    /**
     * 特定期間の更新日の一覧を取得する
     */
    public function getUpdated($from, $to)
    {
        $cond = \ORM::for_table('train_log');
        $cond = $cond->where_gte('updated', $from);
        $cond = $cond->where_lt('updated', $to);
        $ret = $cond->order_by_desc('updated')
            ->distinct()
            ->select('updated')
            ->find_array();

        return $ret;
    }
    /**
     * 特定期間の更新日でフィルタをかけてデータを取得する
     */
    public function getDataByUpdated($from, $to)
    {
        $cond = \ORM::for_table('train_log');
        $cond = $cond->where_gte('updated', $from);
        $cond = $cond->where_lt('updated', $to);
        $ret = $cond->order_by_desc('updated')
            ->find_array();

        return $ret;
    }
    public function countData($from, $to, $col, $colval)
    {
        $cond = \ORM::for_table('train_log');
        $cond = $cond->where_equal($col, $colval);
        $cond = $cond->where_gte('created', $from);
        $cond = $cond->where_lt('created', $to);
        $ret = $cond->count();

        return $ret;
    }

    /**
     * 特定期間のログを削除する.
     */
    public function deleteLogs($deleteTime)
    {
        \ORM::for_table('train_log')
            ->where_lte('created', $deleteTime)
            ->delete_many();
    }

    /**
     * ログを登録
     * @param string $contents 列車ロケーション情報
     */
    public function append($contents)
    {
        $this->db->beginTransaction();
        $updated = time();
        foreach ($contents as $c) {
            $row = \ORM::for_table('train_log')->create();
            $row->ucode = $c->{'urn:ucode'};
            $row->railway = $c->{'odpt:railway'};
            $row->same_as = $c->{'owl:sameAs'};
            $row->train_number = $c->{'odpt:trainNumber'};
            $row->train_type = $c->{'odpt:trainType'};
            $row->delay = $c->{'odpt:delay'};
            $row->starting_station = $c->{'odpt:startingStation'};
            $row->terminal_station = $c->{'odpt:terminalStation'};
            $row->from_station = $c->{'odpt:fromStation'};
            $row->to_station = $c->{'odpt:toStation'};
            $row->rail_direction = $c->{'odpt:railDirection'};
            $row->owner = $c->{'odpt:trainOwner'};
            $row->created = strtotime($c->{'dc:date'});
            $row->valid = strtotime($c->{'dct:valid'});
            $row->updated = $updated;
            $row->save();
        }
        $this->db->commit();
    }
}
