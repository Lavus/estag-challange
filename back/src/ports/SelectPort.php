<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/Select.php";
    error_log(strval('serverlogSTART'));
    foreach($_SERVER as $keylog => $keylogvalue ) {
        if (is_array($keylogvalue)){
            error_log(strval('key : '.$keylog.', ARRAYstart'));
            foreach($keylogvalue as $log => $logvalue) {
                error_log(strval('key : '.$log.', value : '.$logvalue));
            }
            error_log(strval('key : '.$keylog.', ARRAYend'));
        } else {
            error_log(strval('key : '.$keylog.', value : '.$keylogvalue));
        }
    }
    error_log(strval('serverlogEND'));
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT'])) {
            if ($_SERVER['HTTP_I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT'] == 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='){
                $json = file_get_contents('php://input');
                $data = json_decode($json,true);
                error_log($data['code']);
                if ( (!empty($data['type'])) && (!empty($data['table'])) && (isset($data['code'])) ) {
                    echo(json_encode(SelectSql(strval($data['type']),strval($data['table']),strval($data['code']))));
                } else {
                    echo(json_encode(array()));
                }
            } else {
                echo(json_encode(array()));
            }
        } else {
            echo(json_encode(array()));
        }
    } else {
        echo(json_encode(array()));
    }
?>