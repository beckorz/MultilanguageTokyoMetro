<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../config.php';




function test(){
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
  $lang = '';
  $trans = new \MyLib\MsTranslator(MS_AZURE_KEY, $msTranslatorCacheModel, $lang);
  $tmCtrl = new \MyLib\TokyoMetroMultiLangCtrl(END_POINT, TOKYO_METRO_CONSUMER_KEY, $jsonCtrl, $tokyoMetroCacheModel, $trans);

  $ret = $tmCtrl->getStations();
  if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
      var_dump($ret);
      exit();
  }
  $stations = $ret['contents'];

  $railwayType = $tmCtrl->getRailwayType();
  $i = 0;
  print "check error...\n";
  foreach ($stations as $s) {
      print $i . "/" . count($stations) . "\n";
      foreach($railwayType[$s->{'odpt:railway'}]->directions as $d){
          $ret = $tmCtrl->findStationTimetable(array(
              'odpt:station' => $s->{'owl:sameAs'},
              'odpt:railDirection'=>$d));
          if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
              var_dump($ret);
              exit(1);
          }
          foreach( $ret['contents'] as $c) {
              checkStationTimeTable( $s->{'owl:sameAs'}, $d, $c, $tmCtrl);
          }
      }
      $i = $i + 1;
  }
  $trans->updateCacheDb();
}

function checkStationTimeTable($sid, $r, $stationTimetable, $tmCtrl) {
    if ($stationTimetable->{'odpt:weekdays'}) {
        checkTimeTable($sid, $r, 'weekdays', $stationTimetable->{'odpt:weekdays'}, $tmCtrl);
    }
    if ($stationTimetable->{'odpt:saturdays'}) {
        checkTimeTable($sid, $r, 'saturdays', $stationTimetable->{'odpt:saturdays'}, $tmCtrl);
    }
    if ($stationTimetable->{'odpt:holidays'}) {
        checkTimeTable($sid, $r, 'holidays', $stationTimetable->{'odpt:holidays'}, $tmCtrl);
    }
}
function checkTimeTable($sid, $r, $day, $timetable, $tmCtrl) {
    foreach($timetable as $t) {
       if (!$t->{'odpt:trainNumber'}) {
           print $sid . "\t" .$r ."\t" . $day. "\t" . $t->{'odpt:departureTime'} . "\t" . $t->{'odpt:destinationStation'} . "\tNotSettedTrainNo\n";
           continue;
       }
       $ret = $tmCtrl->findTrainTimetable(array(
           'owl:sameAs' => $t->{'odpt:TrainTimetable'}
       ));
       if ($ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
           print $sid . "\t" .$r ."\t" . $day. "\t" . $t->{'odpt:departureTime'} . "\t" . $t->{'odpt:destinationStation'} . "\t errorcode:" .$ret['resultCode'] ."\n";
           continue;
       }
       if ($ret['contents'][0]->{'odpt:terminalStation'} != $t->{'odpt:destinationStation'}) {
           print $sid . "\t" .$r ."\t" . $day. "\t" . $t->{'odpt:departureTime'} . "\t" . $t->{'odpt:destinationStation'} . "\t列車時刻表との不一致\t". $t->{'odpt:TrainTimetable'} . "\t". $ret['contents'][0]->{'odpt:terminalStation'} ."\n";
           continue;
       }
    }
}
test() ;