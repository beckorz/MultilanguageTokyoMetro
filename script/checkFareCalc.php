<?php
/**
 * このスクリプトは東京メトロAPIの運賃取得が適切にどうさするか確認する。
 * 全ての駅の組み合わせに置いて、以下を確認する。
 * ・切符はICより高い事。
 * ・子供料金において切符はICより高い事。
 * ・切符は子供料金の切符より高い事
 * ・ICカードは子供料金のICカードより高いこと
 * ・切符、IC,そしてそれぞれの子供料金は、以下のURLで計算した運賃の結果のいづれかであること。
 *  http://www.tokyometro.jp/ticket/search/index.php
 *  この時、駅名からキーの変換には以下のJSONを用いる。
 *  http://www.tokyometro.jp/common/json/station.json
 * ただし、以下の変換が必要になる。
 *  API側　押上〈スカイツリー前〉　JSON側　押上<スカイツリー前>
 *  API側　鮗ｴ町　JSON側　麹町
 * また、金額があわない場合は、時刻を変えてリトライし、それでも一致しないときはエラーとする。
 */
date_default_timezone_set('Asia/Tokyo');
require 'vendor/autoload.php';
require './config.php';
require_once 'simple_html_dom.php';

function getStationJson($url) {
    $json = file_get_contents($url);
    return json_decode($json);
}

function findNameAlpha($title,$stationJson) {
    $chk = $title;
    if ($title == '押上〈スカイツリー前〉') {
        $chk = '押上<スカイツリー前>';
    }
    if ($title == '鮗ｴ町') {
        $chk = '麹町';
    }
    foreach ($stationJson as $s) {
        if ($s->name == $chk) {
            return $s->name_alph;
        }
    }
    return null;
}

/**
 * 既存システムでの運賃の計算結果を取得する
 * 指定する駅名は以下のjsonのアルファベットのものとなる。
 * http://www.tokyometro.jp/common/json/station.json
 * @param 開始駅名（アルファベット）
 * @param 到着駅名（アルファベット）
 */
function getFare($from, $to)
{
    $client = new \HTTP_Client();
    // TODO. 金額が合わない場合は、時刻を変えてリトライ
    //       エラー時も、内容によってはリトライ
    $param = array(
        'ticSearchName01_01'=>$from,
        'ticSearchName01_02'=>$to,
        'priority'=>'priTransfer', // priTransfer:乗り換え priTime:到達時刻 priFare:運賃
        'month'=>date('Ym'),
        'day'=>date('d'),
        'hour'=>07,
        'minute' => 55,
        'searchOrder'=>'departureTime',
        'fareType'=>'typeBoth',
        'search.x'=>110,
        'search.y'=>36
    );
    $code = $client->get('http://www.tokyometro.jp/ticket/search/index.php', $param);
    $res = $client->currentResponse();
    $body = null;
    $ret = array();
    if ($code != 200) {
        print "Error code:" . $code . "\n";
    }
    if (!$res) {
        return $ret;
    }
    if (!isset($res['body'])) {
        return $ret;
    }
    $dom = str_get_html($res['body']);
    foreach($dom->find('.noticeTxt01') as $list) {
        $price = $list->innertext;
        $pos = strrpos($price,'円');
        if ($pos) {
            array_push($ret,  intval(substr($price, 0, $pos)));
        }
    }
    $dom->clear();
    return $ret;
}
function checkGreater($v1,$v2) {
    if ($v1>$v2) {
        return 'OK';
    }
    return 'NG';
}
function check($v) {
    if ($v) {
        return 'OK';
    }
    return 'NG';
}
/**
 * 運賃計算の試験
 */
function testCalc() 
{
    $fromlist = array(/*
      'odpt.Station:TokyoMetro.Chiyoda.Ayase'
     ,'odpt.Station:TokyoMetro.Marunouchi.Ikebukuro'
     ,'odpt.Station:TokyoMetro.Yurakucho.Ikebukuro'
     ,'odpt.Station:TokyoMetro.Fukutoshin.Ikebukuro'
     ,'odpt.Station:TokyoMetro.Tozai.Urayasu'
     ,'odpt.Station:TokyoMetro.Marunouchi.Ochanomizu'
     ,'odpt.Station:TokyoMetro.Tozai.Kagurazaka'
     ,'odpt.Station:TokyoMetro.Yurakucho.Kanamecho'
     ,'odpt.Station:TokyoMetro.Fukutoshin.Kanamecho'
     ,'odpt.Station:TokyoMetro.Chiyoda.KitaAyase'
     ,'odpt.Station:TokyoMetro.Hibiya.KitaSenju'
     ,'odpt.Station:TokyoMetro.Chiyoda.KitaSenju'
     ,'odpt.Station:TokyoMetro.Tozai.Kiba'
     ,'odpt.Station:TokyoMetro.Ginza.Kyobashi'
     ,'odpt.Station:TokyoMetro.Hanzomon.KiyosumiShirakawa'
     ,'odpt.Station:TokyoMetro.Marunouchi.Korakuen'
     ,'odpt.Station:TokyoMetro.Namboku.Korakuen'
     ,'odpt.Station:TokyoMetro.Yurakucho.KotakeMukaihara'
     ,'odpt.Station:TokyoMetro.Fukutoshin.KotakeMukaihara'
     ,'odpt.Station:TokyoMetro.Marunouchi.Shinjuku'
     ,'odpt.Station:TokyoMetro.Yurakucho.Senkawa'
     ,'odpt.Station:TokyoMetro.Fukutoshin.Senkawa'
     ,'odpt.Station:TokyoMetro.Fukutoshin.Zoshigaya'
     ,'odpt.Station:TokyoMetro.Yurakucho.ChikatetsuAkatsuka'*/
    );
    $tolist = array(/*
     'odpt.Station:TokyoMetro.Hibiya.KitaSenju'
     ,'odpt.Station:TokyoMetro.Chiyoda.KitaAyase'
     ,'odpt.Station:TokyoMetro.Chiyoda.OmoteSando'
     ,'odpt.Station:TokyoMetro.Chiyoda.ShinOchanomizu'
     ,'odpt.Station:TokyoMetro.Hibiya.Kayabacho'
     ,'odpt.Station:TokyoMetro.Chiyoda.KitaAyase'
     ,'odpt.Station:TokyoMetro.Namboku.AkabaneIwabuchi'
     ,'odpt.Station:TokyoMetro.Namboku.Oji'
     ,'odpt.Station:TokyoMetro.Namboku.OjiKamiya'
     ,'odpt.Station:TokyoMetro.Namboku.Komagome'
     ,'odpt.Station:TokyoMetro.Namboku.Shimo'
     ,'odpt.Station:TokyoMetro.Namboku.Todaimae'
     ,'odpt.Station:TokyoMetro.Namboku.Nishigahara'
     ,'odpt.Station:TokyoMetro.Yurakucho.HigashiIkebukuro'
     ,'odpt.Station:TokyoMetro.Namboku.HonKomagome'
     ,'odpt.Station:TokyoMetro.Chiyoda.Ayase'
     ,'odpt.Station:TokyoMetro.Hibiya.Tsukiji'
     ,'odpt.Station:TokyoMetro.Ginza.Mitsukoshimae'
     ,'odpt.Station:TokyoMetro.Ginza.Asakusa'
     ,'odpt.Station:TokyoMetro.Fukutoshin.HigashiShinjuku'
     ,'odpt.Station:TokyoMetro.MarunouchiBranch.Honancho'
     ,'odpt.Station:TokyoMetro.Namboku.Nishigahara'
     ,'odpt.Station:TokyoMetro.Chiyoda.KitaAyase'*/
    );
    $app = new \Slim\Slim();
    $app->setName(APP_NAME);
    $ret = getStationJson('http://www.tokyometro.jp/common/json/station.json');
    $stationJson = $ret->station;
    // 東京メトロAPIの操作を行うクラス
    // 503エラーがあったら、間隔あけてリトライしてるだけ。
    $jsonCtrl = new \MyLib\JsonCtrl(TOKYO_METRO_DATA_DIR);
    $api = new \MyLib\TokyoMetroApi(END_POINT, TOKYO_METRO_DATA_DIR, TOKYO_METRO_CONSUMER_KEY, $jsonCtrl);

    // 東京メトロAPIで駅の一覧取得
    $ret = $api->getStations();
    $stationContents = $ret['contents'];
    print "result.\n";
    $id=0;
    $max = count($stationContents) * (count($stationContents)-1) ;
    $stationContentsTo = $stationContents;
    foreach($stationContents as $fromS) {
        foreach($stationContentsTo as $toS) {
            // おなじsameAsの駅は判定しない
            $fid = $fromS->{'owl:sameAs'};
            $tid = $toS->{'owl:sameAs'};
            if ($fid == $tid) {
                continue;
            }
            // 同名の駅は判定しない。
            $fidName = substr($fid, strrpos($fid,'.')+1);
            $tidName = substr($tid, strrpos($tid,'.')+1);
            if ($fidName == $tidName) {
                continue;
            }
            $id = $id + 1;
            if ($fromlist) {
                if (in_array($fid, $fromlist)== false) {
                    continue;
                }
            }
            if ($tolist) {
                if (in_array($tid, $tolist) == false) {
                    continue;
                }
            }
            //if ($id < 20215) { // リトライ時にすでに特定のところからのみ実行する
            //    continue;
            //}

            // 東京メトロAPIで駅間の運賃取得
            $ret = $api->findFareByFromTo($fid, $tid);
            if( $ret['resultCode'] != \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
                print $fid . "\t" . $tid . "ng" . "\n";
            }
            $fcontents = $ret['contents'];
            foreach($fcontents as $f) {
                if ($f->{'odpt:fromStation'} != $fid) {
                    continue;
                }
                if ($f->{'odpt:toStation'} != $tid) {
                    continue;
                }
                // 既存の運賃計算ページの結果を取得する.
                $exp = getFare(findNameAlpha($fromS->{'dc:title'},$stationJson), findNameAlpha($toS->{'dc:title'},$stationJson));
                $exp_str = implode(",", $exp);
                $greaterTicketIc = checkGreater($f->{'odpt:ticketFare'} , $f->{'odpt:icCardFare'});
                $greaterTicketIcChild = checkGreater($f->{'odpt:childTicketFare'} , $f->{'odpt:childIcCardFare'});
                $greaterTicketChild = checkGreater($f->{'odpt:ticketFare'} , $f->{'odpt:childTicketFare'});
                $greaterIcChild = checkGreater($f->{'odpt:icCardFare'} , $f->{'odpt:childIcCardFare'});
                $inTicketFare= check(in_array($f->{'odpt:ticketFare'}, $exp));
                $inIcCardFare = check(in_array($f->{'odpt:icCardFare'}, $exp));
                $inChildTicketFare= check(in_array($f->{'odpt:childTicketFare'}, $exp));
                $inChildIcCardFare = check(in_array($f->{'odpt:childIcCardFare'}, $exp));
                print $id .'/'. $max . "\t".
                      $fromS->{'owl:sameAs'} . "\t" .
                      $fromS->{'dc:title'} . "\t" .
                      $toS->{'owl:sameAs'} . "\t" .
                      $toS->{'dc:title'} . "\t" .
                      $f->{'odpt:ticketFare'} . "\t" .
                      $f->{'odpt:icCardFare'} . "\t" .
                      $f->{'odpt:childTicketFare'} . "\t" .
                      $f->{'odpt:childIcCardFare'} . "\t".
                      findNameAlpha($fromS->{'dc:title'},$stationJson) . "\t" .
                      findNameAlpha($toS->{'dc:title'},$stationJson) . "\t" .
                      $greaterTicketIc . "\t" .
                      $greaterTicketIcChild . "\t" .
                      $greaterTicketChild . "\t" .
                      $greaterIcChild . "\t" .
                      $inTicketFare. "\t" .
                      $inIcCardFare. "\t" .
                      $inChildTicketFare. "\t" .
                      $inChildIcCardFare. "\t" .
                      $exp_str . "\n";
            }
        }
    }
}
testCalc();
