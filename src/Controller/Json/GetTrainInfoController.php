<?php
namespace Controller\Json;

/**
 * 現在の運行情報を取得する
 */
class GetTrainInfoController extends \Controller\ControllerBase
{
    public function route()
    {
        $tran = $this->modules['MsTranslator'];
        $model = $this->models['TrainLogModel'];
        $tmCtrl = $this->modules['TokyoMetroMultiLangCtrl'];
        $ret = $tmCtrl->findTrainInformation(array());
        if ($ret['resultCode'] !== \MyLib\TokyoMetroApi::RESULT_CODE_OK) {
            $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], null);

            return;
        }
        $railways = $tmCtrl->getRailWayType();
        $res = array();
        $contents = $ret['contents'];
        foreach ($contents as $info) {
            if ($info->{'odpt:trainInformationStatus'}) {
                array_push(
                    $res,
                    array(
                        'railway' => $railways[$info->{'odpt:railway'}]->title,
                        'status' => $info->{'odpt:trainInformationStatus'},
                        'informationText' => $info->{'odpt:trainInformationText'}
                    )
                );
            }
        }
        if (!$res) {
            array_push(
                $res,
                array(
                    'railway' => $tran->translator('全線'),
                    'status' => '',
                    'informationText' => $info->{'odpt:trainInformationText'}
                )
            );
        }
        $this->sendJsonData($ret['resultCode'], $ret['errorMsg'], $res);

        return;
    }
}
