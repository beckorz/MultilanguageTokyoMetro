<?php
require dirname(__FILE__) . '/../vendor/autoload.php';
require dirname(__FILE__) . '/../config.php';

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
