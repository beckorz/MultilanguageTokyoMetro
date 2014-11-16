<?php
date_default_timezone_set('Asia/Tokyo');
require 'vendor/autoload.php';
require './config.php';

//
session_start();
if (DEBUG) {
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
}
else {
  $app = new \Slim\Slim(array(
      'debug' => false,
      'view' => new \Slim\Views\Smarty()
  ));
}
$app->setName(APP_NAME);

$lang = $app->request->params('lang');
if(!$lang) {
    $lang = 'ja';
}

// http://wsf.mot.or.jp/yujakudo/website-admin/use-shared-ssl-of-sakura/
if( isset($_SERVER['HTTP_X_SAKURA_FORWARDED_FOR']) ) {
    $_SERVER['HTTPS'] = 'on';
    $_ENV['HTTPS'] = 'on';
}

// for Smarty.
$view = $app->view();
$view->setTemplatesDirectory(dirname(__FILE__) . '/templates');
$view->parserCompileDirectory = dirname(__FILE__) . '/compiled';
$view->parserCacheDirectory = dirname(__FILE__) . '/cache';

// Database
$existDb = file_exists(DB_PATH);
ORM::configure('sqlite:' . DB_PATH);
$db = ORM::get_db();
// SQLite Likeの対応

$models = array(
    'TokyoMetroCacheModel' => new \Model\TokyoMetroCacheModel($app, $db),
    'MsTranslatorCacheModel' => new \Model\MsTranslatorCacheModel($app, $db),
    'TrainInfoLogModel' => new \Model\TrainInfoLogModel($app, $db),
    'TrainLogModel' => new \Model\TrainLogModel($app, $db),
    'KeyValueModel' => new \Model\KeyValueModel($app, $db),
    'HolidayModel' => new \Model\HolidayModel($app, $db),
    'TranslationLogModel' => new \Model\TranslationLogModel($app, $db)
);

if (!$existDb) {
    $models['TokyoMetroCacheModel']->setup();
    $models['MsTranslatorCacheModel']->setup();
    $models['TrainInfoLogModel']->setup();
    $models['KeyValueModel']->setup();
    $models['HolidayModel']->setup();
    $models['TranslationLogModel']->setup();
}


$jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);


$trans = new \MyLib\MsTranslator(MS_AZURE_KEY, $models['MsTranslatorCacheModel'], $lang);

if (DEBUG) {
    if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on' ) {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
} else {
    $protocol = 'https://';
}

$config = array(
    'blackListAccount' => $GLOBALS[blackListAccount],
    'PYTHON_PATH' => PYTHON_PATH,
    'PHP_PATH' => PHP_PATH
);

$self_url = $protocol.$_SERVER['HTTP_HOST'] . '/' . $app->getName();
$modules = array(
    'MsTranslator' => $trans,
    'JsonCtrl' => $jsonCtrl,
    'TokyoMetroMultiLangCtrl' => new \MyLib\TokyoMetroMultiLangCtrl(END_POINT, TOKYO_METRO_CONSUMER_KEY, $jsonCtrl, $models['TokyoMetroCacheModel'], $trans),
    'TwitterCtrl' => new \MyLib\TwitterCtrl(TW_CONSUMER_KEY, TW_CONSUMER_SECRET,$self_url . '/twitter_login_callback' ),
    'HolidayCtrl' => new \MyLib\HolidayCtrl($models['HolidayModel']),
    'Config' => $config
);


function checkBlackListUser($user) {
    $list = $GLOBALS[blackListAccount];
    if (in_array($user, $list)) {
        return true;
    }
    return false;
}

function redirectUrl($path) {
    $app = \Slim\Slim::getInstance();
    $app->redirect($path);
}

// route middleware for simple API authentication
$authenticateTwitter = function ( $twCtrl, $redirect, $lang ) {
    return function () use ( $twCtrl, $lang) {
        $sts = $twCtrl->getStatus();
        if( $sts !== \MyLib\TwitterCtrl::STATUS_AUTHORIZED ) {
            $authurl = $twCtrl->getAuthorizeUrl();
            $app = \Slim\Slim::getInstance();
            $app->flash('error', 'Login required');
            if($redirect) {
                redirectUrl('/' . $app->getName() . '/login?lang=' . $lang);
            } else {
                $app->halt(401,'Unauthorized');
            }
            return;
        } else {
            if (checkBlackListUser($_SESSION['twitter_user'])) {
               $app->halt(401, 'permission error:' . $_SESSION['twitter_user']);
            }
        }
    };
};

$langInfo = $jsonCtrl->getTranslationInfo();
if (!$langInfo[$lang]) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Not support language.';
    exit();
}

//////////////////////////////////////////////////////////////////////////
// 以下JSON取得用のController
//////////////////////////////////////////////////////////////////////////
/**
 * 駅名の一覧取得
 */
$app->get('/json/get_station_list', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetStationListController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 路線の形状を取得
 */
$app->get('/json/get_railway_region', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetRailwayRegionController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 現在の列車位置情報の取得
 */
$app->get('/json/get_train_location', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetTrainLocationController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 更新日による列車位置情報の履歴の取得
 */
$app->get('/json/get_train_info', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetTrainInfoController($app, $modules, $models);
    $ctrl->route();
});


/**
 * 列車位置情報の履歴を取得
 */
$app->get('/json/get_train_log', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetTrainLogController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 更新日による列車位置情報の履歴の取得
 */
$app->get('/json/find_train_log', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\FindTrainLogController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 運賃を取得
 */
$app->get('/json/calculate_fare', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\CalculateFareController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 翻訳情報を検索して取得する
 */
$app->get('/json/get_translation', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetTranslationController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 翻訳を変更
 */
$app->post('/json/set_translation', 
           $authenticateTwitter($modules['TwitterCtrl'], false, $lang), 
           function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\SetTranslationController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/json/get_translation_log', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetTranslationLogController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/json/get_station_place', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetStationPlaceController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/json/get_station_timetable', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetStationTimetableController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/json/get_translation_data', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Json\GetStationTimetableController($app, $modules, $models);
    $ctrl->route();
});



//////////////////////////////////////////////////////////////////////////
// 以下ページ用のコントローラ
//////////////////////////////////////////////////////////////////////////

/**
 * 運行情報の履歴画面
 */
$app->get('/page/train_info', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\TrainInfoController($app, $modules, $models);
    $ctrl->route();
});


/**
 */
$app->get('/page/railway_info', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\RailwayInfoController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/page/subway_map', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\SubwayMapController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/page/train_location_log', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\TrainLocationLogController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/page/train_timetable', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\TrainTimetableController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/page/station_timetable', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\StationTimetableController($app, $modules, $models);
    $ctrl->route();
});


/**
 * 駅情報ページの表示
 */
$app->get('/page/station_info', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\StationInfoController($app, $modules, $models);
    $ctrl->route();
});

/**
 * 運賃計算用処理
 */
$app->get('/page/calculate_fare',  function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\CalculateFareController($app, $modules, $models);
    $ctrl->route();
});


/**
 * 翻訳修正画面修正
 */
$app->get('/page/translation',  function() use ($app, $modules, $models, $lang) {
    $twCtrl = $modules['TwitterCtrl'];
    $sts = $twCtrl->getStatus();
    
    if( $sts == \MyLib\TwitterCtrl::STATUS_AUTHORIZED ) {
        // 認証済み
        if (checkBlackListUser($_SESSION['twitter_user'])) {
           $app->halt(401,'permission error:' . $_SESSION['twitter_user']);
        }

        $ctrl = new \Controller\Page\TranslationController($app, $modules, $models);
        $ctrl->route();
    } else {
        $_SESSION['callback_url'] = $_SERVER['REQUEST_URI'];
        redirectUrl('/' . $app->getName() . '/login?lang=' . $lang );
    }
});

$app->get('/page/translation_log', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\TranslationLogController($app, $modules, $models);
    $ctrl->route();
});


$app->get('/page/pos_to_train_no', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\PosToTrainNoController($app, $modules, $models);
    $ctrl->route();
});

/**
 * twitterの解析結果の表示
 */
$app->get('/page/twitter_analyze', function() use ($app, $modules, $models, $lang) {
    $twCtrl = $modules['TwitterCtrl'];
    $sts = $twCtrl->getStatus();
    
    if( $sts == \MyLib\TwitterCtrl::STATUS_AUTHORIZED ) {
        $ctrl = new \Controller\Page\TwitterAnalyze($app, $modules, $models);
        $ctrl->route();
    } else {
        $_SESSION['callback_url'] = $_SERVER['REQUEST_URI'];
        redirectUrl('/' . $app->getName() . '/login?lang=' . $lang );
    }

});

$app->get('/', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Page\StartController($app, $modules, $models);
    $ctrl->route();
});

$app->get('/logout', function() use ($app, $modules, $models, $lang) {
    session_destroy();
    session_unset();
    redirectUrl('/' . $app->getName() . '?lang=' . $lang);
});

$app->get('/login', function() use ($app, $modules, $models, $lang) {
    $twCtrl = $modules['TwitterCtrl'];
    $sts = $twCtrl->getStatus();
    if( $sts == \MyLib\TwitterCtrl::STATUS_AUTHORIZED ) {
        if (checkBlackListUser($_SESSION['twitter_user'])) {
           $app->halt(401,'permission error:' . $_SESSION['twitter_user']);
        }

        redirectUrl('/'. $app->getName() .'/page/translation?lang=' . $lang );
    } else {
        $callback = $_SESSION['callback_url'];
        session_regenerate_id(true);
        $_SESSION['callback_url'] = $callback;

        $authurl = $twCtrl->getAuthorizeUrl();
        if ($authurl) {
            redirectUrl($authurl);
        } else {
            $app->halt(500, "Not found twitter authorize url.");
        }
    }
});

$app->get('/twitter_login_callback', function() use ($app, $modules, $models, $lang) {
    $twCtrl = $modules['TwitterCtrl'];
    $sts = $twCtrl->getStatus();
    
    if( $sts == \MyLib\TwitterCtrl::STATUS_REQUEST_ACCESS_TOKEN ) {
        if( $twCtrl->requesAccessToken() ) {
            $rep=$twCtrl->requestVerify();
            if ($rep != null)
            {
                $_SESSION['twitter_user'] = $rep->screen_name;
            } else {
                $twCtrl->reset();
            }
        }
        $sts = $twCtrl->getStatus();
    }

    if( $sts == \MyLib\TwitterCtrl::STATUS_AUTHORIZED ) {
        // 認証済み
        if (checkBlackListUser($_SESSION['twitter_user'])) {
           $app->halt(401,'permission error:' . $_SESSION['twitter_user']);
        }
        if (isset($_SESSION['callback_url'])) {
          redirectUrl($_SESSION['callback_url']);
        }
    }

    redirectUrl('/' . $app->getName() . '/' );
});


//////////////////////////////////////////////////////////////////////////
// 以下テスト用のコントローラ
//////////////////////////////////////////////////////////////////////////
$app->get('/test/honancho', function() use ($app, $modules, $models, $lang) {
    $ctrl = new \Controller\Test\TestHonancho($app, $modules, $models);
    $ctrl->route();
});


//////////////////////////////////////////////////////////////////////////
//
//////////////////////////////////////////////////////////////////////////
$app->hook('slim.after', function () use ($app, $modules, $models, $lang) {
    // 最後にキャッシュを保存しとく.
    $modules['MsTranslator']->updateCacheDb();
});

$app->run();

