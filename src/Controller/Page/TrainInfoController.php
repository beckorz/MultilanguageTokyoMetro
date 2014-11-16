<?php
namespace Controller\Page;

/**
 * 列車運行情報の履歴を表示するページの作成
 */
class TrainInfoController extends \Controller\ControllerBase
{
    public function route()
    {
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $tran = $this->modules['MsTranslator'];
        $traininfos = array();
        $model = $this->models['TrainInfoLogModel'];
        $railways = $tmCtrl->getRailWayType();
        foreach ($railways as $railway_key => $railway_value) {
            if ($railway_value->branch) {
                // 分岐線については表示しない
                continue;
            }
            $logs = $model->getLogs($railway_key, 100, 0);
            $railinfo = array(
                'railwayId'=> $railway_key ,
                'railwayName'=> $railway_value->title ,
                'railwayIcon'=> $railway_value->icon ,
                'log'=>array()
            );
            foreach ($logs as $log) {
                array_push(
                    $railinfo['log'],
                    array(
                       'created' => date("Y/m/d H:i:s", $log->created),
                       'origin' => date("Y/m/d H:i:s", $log->origin),
                       'updated' => date("Y/m/d H:i:s", $log->updated),
                       'status' => $tran->translator($log->status),
                       'information' =>nl2br($tran->translator($log->information))
                    )
                );
            }
            array_push($traininfos, $railinfo);
        }

        $label = array(
            'title' => $tran->translator('列車運行情報'),
            'created' => $tran->translator('データ生成時刻'),
            'origin' => $tran->translator('発生時刻'),
            'updated' => $tran->translator('記録時刻'),
            'status' => $tran->translator('状態'),
            'information' => $tran->translator('運行情報'),
            'check_traininfolog' => $tran->translator('最終API実行日時')
        );
        $tempData = array(
            'appName' => $this->app->getName(),
            'traininfos' => $traininfos,
            'check_traininfolog' => $this->models['KeyValueModel']->get('CHECK_TRAIN_INFO_LOG'),
            'label'=>$label
        );
        $tempData += $this->getHeaderTempalteData();
        $this->app->render('train_info.tpl', $tempData);
    }
}
