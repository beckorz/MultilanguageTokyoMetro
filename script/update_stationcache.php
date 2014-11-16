<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../config.php';




function test($station, $railway){
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

  if (!$existDb) {
      $tokyoMetroCacheModel->setup();
      $msTranslatorCacheModel->setup();
  }
  $jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);

  $trans = new \MyLib\MsTranslator(MS_AZURE_KEY, $msTranslatorCacheModel, $lang);
  $tmCtrl = new \MyLib\TokyoMetroMultiLangCtrl(END_POINT, TOKYO_METRO_CONSUMER_KEY, $jsonCtrl, $tokyoMetroCacheModel, $trans);

  $railwayType = $tmCtrl->getRailwayType();
  foreach($railwayType[$railway]->directions as $d){
      $ret = $tmCtrl->findStationTimetable(array(
          'odpt:station' => $station,
          'odpt:railDirection'=>$d));
      if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
          var_dump($ret);
          exit(1);
      }
  }

  $trans->updateCacheDb();
}

test($argv[1], $argv[2]) ;