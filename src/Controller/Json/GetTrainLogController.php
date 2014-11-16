<?php
namespace Controller\Json;

/**
 * 列車ロケーション情報のログを確認できる。
 * 現在はpageからは利用されていない。.
 */
class GetTrainLogController extends \Controller\ControllerBase
{
    public function route()
    {
        $model = $this->models['TrainLogModel'];

        $limit = 100;
        if ($this->app->request->params('rows')) {
            $limit = $this->app->request->params('rows');
        }
        if ($limit > 10000) {
            $this->app->halt(500, 'limit is too big!!');
        }
        $offset = 0;
        if ($this->app->request->params('page')) {
            $offset = (($this->app->request->params('page') - 1) * $limit);
        }
        $railway = null;
        if ($this->app->request->params('railway')) {
            $railwayS = $this->app->request->params('railway');
        }
        $id =  null;
        if ($this->app->request->params('id')) {
            $id = $this->app->request->params('id');
        }
        $filters = json_decode($this->app->request->params('filters'));
        $ret = $model->getLogs($id, $railway, $limit, $offset);
        foreach ($ret as $key => $row) {
            $ret[$key]['created'] = date("Y/m/d H:i:s", $ret[$key]['created']);
            $ret[$key]['valid'] = date("Y/m/d H:i:s", $ret[$key]['valid']);
            $ret[$key]['updated'] = date("Y/m/d H:i:s", $ret[$key]['updated']);
        }
        header('Content-Type: text/javascript; charset=utf-8');
        print_r(json_encode($ret));
        /*
        $responce->page = $this->app->request->params('page');
        $responce->total = $ret->records/$limit;
        $responce->records = $ret->records;
        $i = 0;
        foreach ($ret->rows as $row) {
          $responce->rows[$i]['id']=$row['id'];
          $responce->rows[$i]['cell']=array($row['id'],
                                            $row['lang'],
                                            $row['src'],
                                            $row['result'],
                                            date("Y/m/d H:i:s", $row['updated']),
                                            $row['author']);
          $i = $i + 1;
        }
        */

        return;
    }
}
