<?php
date_default_timezone_set('Asia/Tokyo');
require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../config.php';
echo "start:" . date("Y/m/d H:i:s",time()). "\n";
$app = new \Slim\Slim(array(
    'debug' => true,
    'log.writer' => new \Slim\Logger\DateTimeFileWriter(array(
                        'path' => './logs',
                        'name_format' => 'Y-m-d',
                        'message_format' => '%label% - %date% - %message%'
                    )),
    'log.enabled' => true,
    'log.level' => \Slim\Log::DEBUG,
    'view' => new \Slim\Views\Smarty()
));
$app->setName(APP_NAME);

// Database
$existDb = file_exists(DB_PATH);
ORM::configure('sqlite:' . DB_PATH);
$db = ORM::get_db();

$tokyoMetroCacheModel = new \Model\TokyoMetroCacheModel($app, $db);
$msTranslatorCacheModel = new \Model\MsTranslatorCacheModel($app, $db);
$trainInfoLogModel = new \Model\TrainInfoLogModel($app, $db);
$trainLogModel = new \Model\TrainLogModel($app, $db);
$keyValueModel = new \Model\KeyValueModel($app, $db);
$holidayModel =  new \Model\HolidayModel($app, $db);

$tokyoMetroCacheModel->setup();
$msTranslatorCacheModel->setup();
$trainInfoLogModel->setup();
$trainLogModel->setup();
$keyValueModel->setup();
$holidayModel->setup();

// 7日間のみ残す
$deletedTime = time() - (3600 * 24 * 7);
$trainLogModel->deleteLogs($deletedTime);
echo "delete time:" . date("Y/m/d H:i:s", $deletedTime). "\n";

$jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);

$lang = $argv[1] ;
$trans = new \MyLib\MsTranslator(MS_AZURE_KEY, $msTranslatorCacheModel, $lang);
$tmCtrl = new \MyLib\TokyoMetroMultiLangCtrl(END_POINT, TOKYO_METRO_CONSUMER_KEY, $jsonCtrl, $tokyoMetroCacheModel, $trans);

// 列車運行情報
$ret = $tmCtrl->findTrainInformation(array());
if ($ret['resultCode'] !== \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
   exit();
}
$infos = $ret['contents'];
$updated = time();
foreach($infos as $info) {
    $sts = '';
    if ($info->{'odpt:trainInformationStatus'}) {
        $sts = $info->{'odpt:trainInformationStatus'};
    }
    $trainInfoLogModel->append($info->{'odpt:railway'},
                               $info->{'odpt:operator'},
                               strtotime($info->{'dc:date'}),
                               strtotime($info->{'odpt:timeOfOrigin'}),
                               $updated,
                               $sts,
                               $info->{'odpt:trainInformationText'});
}
$keyValueModel->set('CHECK_TRAIN_INFO_LOG', date("Y/m/d H:i:s",time()));

// 列車位置情報
$ret = $tmCtrl->findTrain(array());
if ($ret['resultCode'] !== \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
   exit();
}
$contents = $ret['contents'];
$trainLogModel->append($contents);

// 言語情報の更新
$updateLangCache = $keyValueModel->get('UPDATE_LANG_CACHE');
if (!$updateLangCache) {
  $updateLangCache = 0;
}
if (time() > $updateLangCache + (3600 * 24)) {
  $txt = PHP_PATH . ' ' . dirname(__FILE__) . '/updatelang.php';
  echo $txt . "\n";
  $ret = exec($txt,$output,$retval);
  var_dump($output);
  var_dump($retval);
}


$keyValueModel->set('CHECK_TRAIN_LOG', date("Y/m/d H:i:s",time()));
echo "end:" . date("Y/m/d H:i:s",time()) . "\n";

