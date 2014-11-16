<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../config.php';
date_default_timezone_set('Asia/Tokyo');
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

$view = $app->view();
$view->setTemplatesDirectory(dirname(__FILE__) . '/../templates');
$view->parserCompileDirectory = dirname(__FILE__) . '/../compiled';
$view->parserCacheDirectory = dirname(__FILE__) . '/../cache';


function updateTran($app, $models,$jsonCtrl ,$lang) {
  echo $lang . "......\n";
  $trans = new \MyLib\MsTranslator(MS_AZURE_KEY, $models['MsTranslatorCacheModel'], $lang);
  $tmCtrl = new \MyLib\TokyoMetroMultiLangCtrl(END_POINT, TOKYO_METRO_CONSUMER_KEY, $jsonCtrl, $models['TokyoMetroCacheModel'] ,$trans);
  $modules = array(
      'MsTranslator' => $trans,
      'JsonCtrl' => $jsonCtrl,
      'TokyoMetroMultiLangCtrl' => $tmCtrl,
      'HolidayCtrl' => new \MyLib\HolidayCtrl($models['HolidayModel'])
  );
  $tmCtrl->getPassengerSurvey();
  $tmCtrl->getStations();
  $tmCtrl->getRailways();
  $tmCtrl->getPois();
  $tmCtrl->getStationFacilities();
  $tmCtrl->getOtherStationDict();
  $tmCtrl->getTrainType();
  $tmCtrl->getTrainOwnerType();
  $tmCtrl->getOperatorType();
  $tmCtrl->getRailWayType();
  $tmCtrl->getOtherRailWayType();
  $tmCtrl->getRailDirectionType();

  // 運行情報
  $ctrl = new \Controller\Page\TrainInfoController($app, $modules, $models);
  $ctrl->route();

  $trans->updateCacheDb();
}

// Database
$existDb = file_exists(DB_PATH);
ORM::configure('sqlite:' . DB_PATH);
$db = ORM::get_db();

$models = array(
    'TokyoMetroCacheModel' => new \Model\TokyoMetroCacheModel($app, $db),
    'MsTranslatorCacheModel' => new \Model\MsTranslatorCacheModel($app, $db),
    'TrainInfoLogModel' => new \Model\TrainInfoLogModel($app, $db),
    'TrainLogModel' => new \Model\TrainLogModel($app, $db),
    'KeyValueModel' => new \Model\KeyValueModel($app, $db),
    'HolidayModel' => new \Model\HolidayModel($app, $db),
    'TranslationLogModel' => new \Model\TranslationLogModel($app, $db)
);
$models['TokyoMetroCacheModel']->setup();
$models['MsTranslatorCacheModel']->setup();
$models['TrainInfoLogModel']->setup();
$models['KeyValueModel']->setup();
$models['HolidayModel']->setup();
$models['TranslationLogModel']->setup();

$jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);


$lang = $argv[1] ;
try {
  if($lang) {
    updateTran($app, $models,$jsonCtrl ,$lang);
  } else {
    $models['KeyValueModel']->set('UPDATE_LANG_CACHE', time());
    $tranInfo = $jsonCtrl->getTranslationInfo();
    foreach ($tranInfo as $key => $item) {
      updateTran($app, $models, $jsonCtrl ,$key);
    }
    $models['KeyValueModel']->set('UPDATE_LANG_CACHE_END', time()); 
  }
} catch (Exception $e) {
    echo '例外: ',  $e->getMessage(), "\n";
} 

