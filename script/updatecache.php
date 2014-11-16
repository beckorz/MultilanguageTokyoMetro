<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../config.php';




function test($lang){
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

  $tokyoMetroCacheModel->clearContents();

  echo "getPassengerSurvey\n";
  $ret = $tmCtrl->getPassengerSurvey();
  if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
      var_dump($ret);
      exit();
  }
  unset($ret);

  echo "getStations\n";
  $ret = $tmCtrl->getStations();
  if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
      var_dump($ret);
      exit();
  }
  $stations = $ret['contents'];

  echo "getRailways\n";
  $ret = $tmCtrl->getRailways();
  if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
      var_dump($ret);
      exit();
  }
  unset($ret);

  echo "getPois\n";
  $ret = $tmCtrl->getPois();
  if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
      var_dump($ret);
      exit();
  }
  unset($ret);

  echo "getStationFacilities\n";
  $ret = $tmCtrl->getStationFacilities();
  if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
      var_dump($ret);
      exit();
  }
  unset($ret);

  echo  "stationTimeTable ... TODO\n";
  $railwayType = $tmCtrl->getRailwayType();
  $i = 0;
  foreach ($stations as $s) {
      echo $i . "/" .count($stations) . ":".  memory_get_usage(). "\n";
      $txt = PHP_PATH . ' ' . dirname(__FILE__) . '/update_stationcache.php ' . $s->{'owl:sameAs'} . " " . $s->{'odpt:railway'};
      $ret = exec($txt,$output,$retval);
      if ($retval != 0 ) {
          var_dump($output);
          return;
      }
      $i = $i + 1;
  }
  $trans->updateCacheDb();
}

test($argv[1]) ;