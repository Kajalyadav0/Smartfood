<?php
namespace StartklarElmentorFormsExtWidgets;
use  TP_MaxMind\Db\Reader;

class startklarCountrySelectorProcess {
    static public function process(){
        require_once(__DIR__ . "/lib/GeoLocator/src/autoload.php");
        $ret_arr = [];
        if (isset($_SERVER["REMOTE_ADDR"]) && !empty($_SERVER["REMOTE_ADDR"]) && preg_match("/\d+.\d+.\d+.\d+/ism", $_SERVER["REMOTE_ADDR"], $matches)) {
            $reader = new Reader(__DIR__ . "/lib/GeoLocator/src/GeoLite2-Country/GeoLite2-Country.mmdb");
            $test = $reader->get($_SERVER["REMOTE_ADDR"]);
            //$test = $reader->get("31.41.77.246");
            if (isset($test["country"]["names"]["en"]) && !empty($test["country"]["names"]["en"])) {
                $ret_arr = ["country" => $test["country"]["names"]["en"]];
            }
        }
        echo json_encode($ret_arr);
        exit;
    }
}