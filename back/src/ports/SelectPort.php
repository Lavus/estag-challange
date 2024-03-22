<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/Select.php";
    // error_log(strval('serverlogSTART'));
    // foreach($_SERVER as $keylog => $keylogvalue ) {
    //     if (is_array($keylogvalue)){
    //         error_log(strval('key : '.$keylog.', ARRAYstart'));
    //         foreach($keylogvalue as $log => $logvalue) {
    //             error_log(strval('key : '.$log.', value : '.$logvalue));
    //         }
    //         error_log(strval('key : '.$keylog.', ARRAYend'));
    //     } else {
    //         error_log(strval('key : '.$keylog.', value : '.$keylogvalue));
    //     }
    // }
    // error_log(strval('serverlogEND'));
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT'])) {
            if ($_SERVER['HTTP_I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT'] == 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='){
                $json = file_get_contents('php://input');
                $data = json_decode($json,true);
                error_log($data['code']);
                if ( (!empty($data['type'])) && (!empty($data['table'])) && (isset($data['code'])) && (!empty($data['camps'])) ) {
                    if ( ($data['type'] == "FullSimple") || ($data['type'] == "SingleSimple") ) {
                        echo(json_encode(SelectSql(strval($data['type']),strval($data['table']),strval($data['code']),$data['camps'])));
                    } else if ( $data['type'] == "SimpleForeign" ) {
                        if ( (!empty($data['innerCamps'])) && (!empty($data['innerCampsAlias'])) && (!empty($data['innerTable'])) && (!empty($data['foreignKey'])) ) {
                            echo( json_encode( SelectSql( strval($data['type']), strval($data['table']), strval($data['code']), $data['camps'], $data['innerCamps'], $data['innerCampsAlias'], strval($data['innerTable']), strval($data['foreignKey']) ) ) );
                        } else {
                            echo(json_encode(array()));
                        }
                    } else if ( $data['type'] == "FullCases" ) {
                        if ( (isset($data['innerCamps'])) && (isset($data['innerCampsAlias'])) && (isset($data['innerTable'])) && (isset($data['foreignKey'])) && (isset($data['where'])) && (!empty($data['caseVerifications'])) && (!empty($data['caseVerificationTables'])) && (!empty($data['caseVerificationTablesAlias'])) && (!empty($data['caseVerificationWheres'])) && (!empty($data['caseVerificationParameters'])) && (!empty($data['caseVerificationValues'])) && (!empty($data['caseVerificationValueTables'])) && (!empty($data['caseVerificationValueTablesAlias'])) && (!empty($data['caseVerificationValueWheres'])) && (!empty($data['caseVerificationElse'])) && (!empty($data['caseVerificationAlias'])) ) {
                            echo( json_encode( SelectSql( strval($data['type']), strval($data['table']), strval($data['code']), $data['camps'], $data['innerCamps'], $data['innerCampsAlias'], strval($data['innerTable']), strval($data['foreignKey']), strval($data['where']), $data['caseVerifications'], $data['caseVerificationTables'], $data['caseVerificationTablesAlias'], $data['caseVerificationWheres'], $data['caseVerificationParameters'], $data['caseVerificationValues'], $data['caseVerificationValueTables'], $data['caseVerificationValueTablesAlias'], $data['caseVerificationValueWheres'], $data['caseVerificationElse'], $data['caseVerificationAlias'] ) ) );
                        } else {
                            echo(json_encode(array()));
                        }
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
    } else {
        echo(json_encode(array()));
    }
?>