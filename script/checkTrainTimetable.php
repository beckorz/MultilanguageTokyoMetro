<?php
/**

 */
date_default_timezone_set('Asia/Tokyo');
require 'vendor/autoload.php';
require './config.php';

function check($exp, $act) {
    if ($exp === $act) {
        return 'OK';
    }
    return 'NG('. $exp .' vs ' .$act .')';
}


/**
 * TrainTimetableの試験
 */
function test($from, $to) 
{
    
    $app = new \Slim\Slim();
    $app->setName(APP_NAME);
    // 東京メトロAPIの操作を行うクラス
    // 503エラーがあったら、間隔あけてリトライしてるだけ。
    $jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);
    $api = new \MyLib\TokyoMetroApi(END_POINT, TOKYO_METRO_CONSUMER_KEY, $jsonCtrl);


    ORM::configure('sqlite:' . DB_PATH);
    $db = ORM::get_db();

    $holidayModel = new \Model\HolidayModel($app, $db);
    $holidayModel->setup();
    $holidayCtrl = new \MyLib\HolidayCtrl($holidayModel);

    $model = new \Model\TrainLogModel($app, $db);
    $trains = $model->getDataByUpdated(strtotime($from), strtotime($to));
    $i = 0;
    $checked = array();
    foreach($trains as $t) {
        $i  = $i +1;

        $daytype = $holidayCtrl->checkDayType($t['created']);
        $key = $daytype . $t['same_as'];
        if ($checked[$key]) {
            continue;
        }
        $checked += array($key => true);
        $param = array('odpt:train'=>$t['same_as']);
        $ret = $api->findTrainTimetable($param);
        if( $ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            print ($t['same_as'] . "\t" . "NG\t" . $ret['errorMsg'] .  "\n");
            continue;
        }
        $count = 0;
        foreach($ret['contents'] as $c) {
            if ($c->{'odpt:train'} != $t['same_as']) {
                continue;
            }

            if (!$c->{'odpt:'.$daytype}) {
                // 指定の曜日がないデータは検査しない
                continue;
            }
            print (
              $i . "/". count($trains) . "\t" . 
              date('Y/m/d h:i:s', $t['created']) . "\t" . 
              $t['same_as'] . "\t" . 
              check($t['railway'], $c->{'odpt:railway'}) . "\t" .
              check($t['train_number'], $c->{'odpt:trainNumber'}) . "\t" .
              check($t['train_type'], $c->{'odpt:trainType'}) . "\t" .
              check($t['terminal_station'], $c->{'odpt:terminalStation'}) . "\t" .
              check($t['rail_direction'], $c->{'odpt:railDirection'}) . "\t" .
              check($t['owner'], $c->{'odpt:trainOwner'}) . "\n"
            );
            $count = $count + 1;
        }
        if ($count == 0) {
            print ($t['same_as'] . "\tNG\tNotfound\n");
        }
    }

}
test($argv[1], $argv[2]);
