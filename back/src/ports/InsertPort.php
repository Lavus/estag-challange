<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/Select.php";
    require_once __DIR__."/../security/CheckValidityCode.php";
    
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
        if (!empty($_SERVER['HTTP_FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44'])) {
            if ($_SERVER['HTTP_FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44'] == 'Falw1qKPKZYufBz0r2S1avMZ16BeNHPn3/nqJzg2IyDHF+XtM4x9cBMTOvG++LTO3wCbTEJXEocIO+xfjPCEunNGKu8DvjQzXG29DSSiuQsPnwVV+/cHwnNh6MFLg3KvNC4k3v9uhXZkRMBaRIglt2FnKt3gLssn'){
                $json = trim(file_get_contents('php://input'));
                $data = json_decode($json,true);
                $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
                $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
                if ( !empty($data['type']) ) {
                    if($data['type'] == "categories"){
                        echo(json_encode(array('result'=>true)));
                    } else {
                        echo(json_encode(array()));
                    }
                } else {
                    echo(json_encode(array('result'=>false)));
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
    //             if ( (!empty($data['type'])) && (!empty($data['table'])) && (isset($data['code'])) && (!empty($data['camps'])) && (!empty($data['campsAlias'])) ) {
    //                 if ( (strval($data['code']) !== '0') && (intval($data['code']) === 0) ){
    //                     echo(json_encode(array("broken"=>"broken")));
    //                 } else if  ( (CheckValidityCode($data['code'],$data['table'])) && ( $data['type'][0] != "SimpleWhere" ) || (strval($data['code']) === '0') || ( ( CheckValidityCode($data['code'],$data['innerTables'][0]) ) && ( (count($data['type']) == 2) && ( $data['type'][0] == "SimpleWhere" ) ) ) ) {
    //                     error_log("entrou");
    //                     if (count($data['type']) == 1){
    //                         if ( ($data['type'][0] == "FullSimple") || ($data['type'][0] == "SingleSimple") ) {
    //                             echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'] ) ) );
    //                         } else if ( $data['type'][0] == "SimpleForeign" ) {
    //                             if ( (!empty($data['innerCamps'])) && (!empty($data['innerCampsAlias'])) && (!empty($data['innerTables'])) && (!empty($data['foreignKey'])) ) {
    //                                 echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']) ) ) );
    //                             } else {
    //                                 echo(json_encode(array()));
    //                             }
    //                         } else if ( $data['type'][0] == "SimpleWhere" ) {
    //                             if ( (isset($data['innerCamps'])) && (isset($data['innerCampsAlias'])) && (isset($data['innerTables'])) && (isset($data['foreignKey'])) && (!empty($data['where'])) ) {
    //                                 echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']), strval($data['where']) ) ) );
    //                             } else {
    //                                 echo(json_encode(array()));
    //                             }
    //                         } else if ( ( $data['type'][0] == "FullCases" ) || ( $data['type'][0] == "FullCasesHome" ) ){
    //                             if ( (isset($data['innerCamps'])) && (isset($data['innerCampsAlias'])) && (isset($data['innerTables'])) && (isset($data['foreignKey'])) && (isset($data['where'])) && (!empty($data['caseVerifications'])) && (!empty($data['caseVerificationTables'])) && (!empty($data['caseVerificationTablesAlias'])) && (!empty($data['caseVerificationWheres'])) && (!empty($data['caseVerificationParameters'])) && (!empty($data['caseVerificationValues'])) && (!empty($data['caseVerificationValueTables'])) && (!empty($data['caseVerificationValueTablesAlias'])) && (!empty($data['caseVerificationValueWheres'])) && (!empty($data['caseVerificationElse'])) && (!empty($data['caseVerificationAlias'])) ) {
    //                                 echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']), strval($data['where']), $data['caseVerifications'], $data['caseVerificationTables'], $data['caseVerificationTablesAlias'], $data['caseVerificationWheres'], $data['caseVerificationParameters'], $data['caseVerificationValues'], $data['caseVerificationValueTables'], $data['caseVerificationValueTablesAlias'], $data['caseVerificationValueWheres'], $data['caseVerificationElse'], $data['caseVerificationAlias'] ) ) );
    //                             } else {
    //                                 echo(json_encode(array()));
    //                             }
    //                         }                    
    //                     } else if ( (count($data['type']) == 2) && ($data['type'][0] == "TooComplex") ) {
    //                         echo( json_encode( SelectSql( $data['type'] ) ) );
    //                     } else if ( (count($data['type']) == 2) && ( $data['type'][0] == "SimpleWhere" ) ) {
    //                             if ( (isset($data['innerCamps'])) && (isset($data['innerCampsAlias'])) && (isset($data['innerTables'])) && (isset($data['foreignKey'])) && (!empty($data['where'])) ) {
    //                                 echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']), strval($data['where']) ) ) );
    //                             } else {
    //                                 echo(json_encode(array()));
    //                             }
    //                     } else {
    //                         echo(json_encode(array()));
    //                     }
    //                 } else {
    //                     echo(json_encode(array("broken"=>"broken")));
    //                 }
    //             } else {
    //                 echo(json_encode(array()));
    //             }
    //         } else {
    //             echo(json_encode(array()));
    //         }
    //     } else {
    //         echo(json_encode(array()));
    //     }
    // } else {
    //     echo(json_encode(array('result'=>true)));
    // }
?>