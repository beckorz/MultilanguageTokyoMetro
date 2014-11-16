<?php
namespace Controller\Test;

/**
 * railwayパラメータにて路線による絞込みができるものとする.
 */
class TestHonancho extends \Controller\ControllerBase
{
    public function route()
    {
        $model = $this->models['TrainLogModel'];
        $from = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5);
        $to = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5-3600);
        print date("Y/m/d H:i:s", time());
        print " Honancho train data   <BR>";
        print "starting station -----------";
        print "<BR>";
        while (true) {
            $ret = $model->countData(
                $from,
                $to,
                'starting_station',
                'odpt.Station:TokyoMetro.MarunouchiBranch.Honancho'
            );
            print date("Y/m/d H:i:s", $from);
            print "-";
            print date("Y/m/d H:i:s", $to);
            print "-";
            print $ret;
            print "<BR>";
            $from = $from + 3600;
            $to = $to + 3600;
            if (time() < $from) {
                break;
            }
        }
        print "<BR>terminal station ------------";
        $from = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5);
        $to = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5-3600);
        print "<BR>";
        while (true) {
            $ret = $model->countData(
                $from,
                $to,
                'terminal_station',
                'odpt.Station:TokyoMetro.MarunouchiBranch.Honancho'
            );
            print date("Y/m/d H:i:s", $from);
            print "-";
            print date("Y/m/d H:i:s", $to);
            print "-";
            print $ret;
            print "<BR>";
            $from = $from + 3600;
            $to = $to + 3600;
            if (time() < $from) {
                break;
            }
        }

        print "<BR>from station ------------";
        $from = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5);
        $to = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5-3600);
        print "<BR>";
        while (true) {
            $ret = $model->countData(
                $from,
                $to,
                'from_station',
                'odpt.Station:TokyoMetro.MarunouchiBranch.Honancho'
            );
            print date("Y/m/d H:i:s", $from);
            print "-";
            print date("Y/m/d H:i:s", $to);
            print "-";
            print $ret;
            print "<BR>";
            $from = $from + 3600;
            $to = $to + 3600;
            if (time() < $from) {
                break;
            }
        }

        print "<BR>to station ------------";
        $from = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5);
        $to = strtotime(date("Y/m/d H:0:0", time()))-(3600*24*5-3600);
        print "<BR>";
        while (true) {
            $ret = $model->countData(
                $from,
                $to,
                'to_station',
                'odpt.Station:TokyoMetro.MarunouchiBranch.Honancho'
            );
            print date("Y/m/d H:i:s", $from);
            print "-";
            print date("Y/m/d H:i:s", $to);
            print "-";
            print $ret;
            print "<BR>";
            $from = $from + 3600;
            $to = $to + 3600;
            if (time() < $from) {
                break;
            }
        }

        return;
    }
}
