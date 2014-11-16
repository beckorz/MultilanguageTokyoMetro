<?php
namespace Controller;

abstract class ControllerBase
{
    protected $app;
    protected $modules;
    protected $models;
    public function __construct($app, $modules, $models)
    {
        $this->app = $app;
        $this->modules = $modules;
        $this->models = $models;
        $this->lang = $this->app->request->params('lang');
        if (!$this->lang) {
            $this->lang = 'ja';
        }
    }

    abstract public function route();

    protected function sendJsonData($retcode, $errormsg, $contents)
    {
        $this->app->contentType('Content-Type: application/json;charset=utf-8');
        $data = array(
            'resultCode' => $retcode,
            'errorMsg' => $errormsg,
            'contents' => $contents
        );
        print_r(json_encode($data));
    }
    protected function removeSymbol($value)
    {
        return preg_replace('/[][}{)(!"#$%&\'~|\*+,\/@.\^<>`;:?_=\\\\-]/i', '', $value);
    }
    protected static function getName($path)
    {
        if ($path) {
            return substr($path, strrpos($path, '.')+1);
        } else {
            return $path;
        }
    }

    /**
     * 駅コードより、ロゴのURLを取得する
     * @param  string $stationCode 駅コード
     * @return string ロゴのURL
     */
    protected function getStationCodeLogoUrl($stationCode)
    {
        if (strpos($stationCode, 'm') === 0) {
            return '/' . $this->app->getName() . '/img/logo/m'. $stationCode . '.png';
        } else {
            return '/' . $this->app->getName() . '/img/logo/'. $stationCode . '.png';
        }
    }

    /**
     * Headerのレンダリング用のデータ取得
     */
    protected function getHeaderTempalteData()
    {
        $tran = $this->modules['MsTranslator'];
        $headLabel = array(
          'selectLang' =>  $tran->translator('表示する言語を選択してください。'),
          'contact' =>  $tran->translator('連絡先'),
          'translation' =>  $tran->translator(
              'Twitterのアカウントでログインをして、テキストの修正を行います。'
          ),
          'railway' =>  $tran->translator('路線情報を表示します。'),
          'start' =>  $tran->translator('スタートページ'),
          'train_info_log' =>  $tran->translator('運行情報の履歴'),
          'pos_to_train_no' =>  $tran->translator('現在位置から列車検索'),
          'calc_fare' =>  $tran->translator('運賃の計算'),
          'train_location_log' =>  $tran->translator('列車位置情報の履歴(一覧)'),
          'train_location_log_map' =>  $tran->translator('列車位置情報の履歴(マップ)'),
          'translation_log' =>  $tran->translator('テキスト変更履歴'),
          'login' =>  $tran->translator('ログイン'),
          'logout' =>  $tran->translator('ログアウト')
        );
        $twitterLabel = array(
          'searchHashTitle' =>  $tran->translator('ツイッターで検索'),
          'alanyzeTermHashTitle' =>  $tran->translator('ハッシュタグによる検索結果の解析'),
          'alanyzePosTitle' =>  $tran->translator('位置による検索結果の解析')
        );
        $transInfo = $this->modules['JsonCtrl']->getTranslationInfo();
        $ret = array(
          'langList' => $transInfo,
          'lang' => $this->lang,
          'rtl' => $transInfo[$this->lang]->rtl,
          'user' => $_SESSION['twitter_user'],
          'headLabel' => $headLabel,
          'twitterLabel' => $twitterLabel
        );
        return $ret;
    }

    /**
     * 任意のページへのリンクを作成する
     * この関数は自動でlangパラメータを付与する
     * @param string path "/' . $this->app->getName() . '/"以下のパスを指定する。
     */
    protected function createPageUrl($path, $query)
    {
        $url = '/' . $this->app->getName() . '/page/' . $path;
        if ($query) {
            $url = $url . '?' . $query . '&lang=' . $this->lang;
        } else {
            $url = $url . '?lang=' . $this->lang;
        }

        return $url;
    }

    /**
     * 駅時刻表へのリンクの作成
     */
    protected function createTimetableLink($target, $railwayType, $railDirectionType)
    {
        $timetable = array();
        $sNameArray = explode('.', $target->{'owl:sameAs'});
        $directList = $railwayType[$target->{'odpt:railway'}]->directions;
        if ($railwayType[$target->{'odpt:railway'}]->branchDirectionCond) {
            $condInfo = $railwayType[$target->{'odpt:railway'}]->branchDirectionCond->{$target->{'owl:sameAs'}};
            if ($condInfo) {
                if ($condInfo->mergeMain) {
                    $directList = array_merge($directList, $railwayType[$target->{'odpt:railway'}]->branchDirections);
                } else {
                    $directList = $railwayType[$target->{'odpt:railway'}]->branchDirections;
                }
            }
        }
        foreach ($directList as $d) {

            $dNameArray=explode('.', $d);
            // 駅名と方向名に含まれる駅が等しければ、それ以上、その方面には行けない
            if ($dNameArray[count($dNameArray)-1] === $sNameArray[count($sNameArray)-1]) {
                continue;
            }
            array_push(
                $timetable,
                $this->createTimeTableData($d, $target->{'owl:sameAs'}, $railDirectionType)
            );
        }
        if ($target->{'owl:sameAs'} == 'odpt.Station:TokyoMetro.Marunouchi.NakanoSakaue') {
            array_push(
                $timetable,
                $this->createTimeTableData(
                    'odpt.RailDirection:TokyoMetro.Honancho',
                    $target->{'owl:sameAs'},
                    $railDirectionType
                )
            );
        }
        return $timetable;
    }

    /**
     * 駅時刻表用のデータを作成
     * @param $d 方向
     * @param $staion 駅コード
     * @param $railDirectionType 方向情報を格納しているテーブル
     * @return array 駅時刻表用のデータ
     */
    private function createTimeTableData($d, $staion, $railDirectionType)
    {
        return array(
            'direction'=>$d,
            'directionTitle'=>$railDirectionType[$d],
            'directionUrl'=> $this->createPageUrl(
                'station_timetable',
                'station=' . $staion . '&direction=' . $d
            )
        );
    }

    /**
     * モバイルかどうかのチェック
     */
    protected function isMobile()
    {
        $isMobile = $this->app->request->params('mobile');
        if (!$isMobile) {
            $delect = new \Mobile_Detect;
            $isMobile = $delect->isMobile();
        }
        return $isMobile;
    }
}
