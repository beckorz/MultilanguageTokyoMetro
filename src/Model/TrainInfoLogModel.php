<?php
namespace Model;

/**
 * 東京メトロAPIの運行情報を記録するモデル<br>
 */
class TrainInfoLogModel extends \Model\ModelBase
{
    /**
     * データベースの設定を行う.
     */
    public function setup()
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS train_info_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                railway TEXT,
                operator TEXT,
                created TIMESTAMP,
                origin TIMESTAMP,
                updated TIMESTAMP,
                status TEXT,
                information TEXT
            );"
        );
        $this->db->exec(
            "CREATE INDEX IF NOT EXISTS train_info_log_railway_index ON  train_info_log(railway);"
        );
        $this->db->exec(
            "CREATE INDEX IF NOT EXISTS train_info_log_created_index ON  train_info_log(created);"
        );
    }

    /**
     * 特定の路線の運行情報を新しいもの順で取得する
     * @param  string $railway 路線名
     * @param  string $limit   取得件数
     * @param  string $offset  取得開始位置
     * @return .
     */
    public function getLogs($railway, $limit, $offset)
    {
        $ret = \ORM::for_table('train_info_log')
            ->where_equal('railway', $railway)
            ->limit($limit)
            ->offset($offset)
            ->order_by_desc('created')
            ->find_many();

        return $ret;
    }

    /**
     * ログを登録
     * @param string $railway     路線名
     * @param string $operator    運行会社
     * @param int    $created     データ作成日のタイムスタンプ
     * @param int    $origin      事象発生日時のタイムスタンプ
     * @param int    $updated     API実行日時のタイムスタンプ
     * @param int    $status      ステータス
     * @param int    $information 情報テキスト
     */
    public function append($railway, $operator, $created, $origin, $updated, $status, $information)
    {
        $lastdata = $this->getLogs($railway, 1, 0);
        if (count($lastdata > 0)) {
            if ($lastdata[0]->information == $information) {
                // 直近の記録文字と等しい場合は何もしない
                return;
            }
        }
        if (!is_string($status)) {
            $status = 'status is unexpected type.';
        }
        $row = \ORM::for_table('train_info_log')->create();
        $row->railway = $railway;
        $row->operator = $operator;
        $row->created = $created;
        $row->origin = $origin;
        $row->updated = $updated;
        $row->status = $status;
        $row->information = $information;
        $row->save();
    }
}
