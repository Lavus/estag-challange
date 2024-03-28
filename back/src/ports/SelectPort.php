<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/SelectSql.php";
    require_once __DIR__."/../security/CheckValidityCode.php";
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_VZFUYQREU2LGC3GNSGG6OMHPIZDTCMRBO4U6K6TL34OFPETOJUHICKGI2VC0IFESXISM3CO2U4JQIWFIHLGWH1H2PQYOZYY47VXPS31GRJETRKJJRXIT4WA'])) {
            if ($_SERVER['HTTP_VZFUYQREU2LGC3GNSGG6OMHPIZDTCMRBO4U6K6TL34OFPETOJUHICKGI2VC0IFESXISM3CO2U4JQIWFIHLGWH1H2PQYOZYY47VXPS31GRJETRKJJRXIT4WA'] == '7YsnXD6n7DYWfqhrh0laPlOQ9KDNUJewxyvURCrI5mL1foDtPWsjRTxdBKgf3wT5QaJYo8D8hpqftMbcTtPdDQpiUwDDZg0O5E0GulikcL7ncvzfYYYlutIkqaNHTOsAvyTYsHHuuUN4Fl2qHEkoC5D1qY+OMWE='){
                $json = trim(file_get_contents('php://input'));
                $data = json_decode($json,true);
                if ( (!empty($data['type'])) && (!empty($data['table'])) && (isset($data['code'])) && (!empty($data['camps'])) && (!empty($data['campsAlias'])) ) {
                    if ( (strval($data['code']) !== '0') && (intval($data['code']) === 0) ){
                        echo(json_encode(array("broken"=>"broken")));
                    } else if  ( ( (CheckValidityCode($data['code'],$data['table'])) && (count($data['type']) == 1) ) || (strval($data['code']) === '0') || ( ( CheckValidityCode($data['code'],$data['innerTables'][0],$data['innerTables'][0].'.code = '.$data['code'].' AND '.$data['innerTables'][0].'.code NOT IN ( SELECT MAX( orders1.code ) FROM orders AS orders1 ) ORDER BY '.$data['innerTables'][0].'.code;') ) && ( (count($data['type']) == 2) && ( $data['type'][0] == "SimpleWhere" ) ) ) ) {
                        if (count($data['type']) == 1){
                            if ( ($data['type'][0] == "FullSimple") || ($data['type'][0] == "SingleSimple") ) {
                                echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'] ) ) );
                            } else if ( $data['type'][0] == "SimpleForeign" ) {
                                if ( (!empty($data['innerCamps'])) && (!empty($data['innerCampsAlias'])) && (!empty($data['innerTables'])) && (!empty($data['foreignKey'])) ) {
                                    echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']) ) ) );
                                } else {
                                    echo(json_encode(array()));
                                }
                            } else if ( $data['type'][0] == "SimpleWhere" ) {
                                if ( (isset($data['innerCamps'])) && (isset($data['innerCampsAlias'])) && (isset($data['innerTables'])) && (isset($data['foreignKey'])) && (!empty($data['where'])) ) {
                                    echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']), strval($data['where']) ) ) );
                                } else {
                                    echo(json_encode(array()));
                                }
                            } else if ( ( $data['type'][0] == "FullCases" ) || ( $data['type'][0] == "FullCasesHome" ) ){
                                if ( (isset($data['innerCamps'])) && (isset($data['innerCampsAlias'])) && (isset($data['innerTables'])) && (isset($data['foreignKey'])) && (isset($data['where'])) && (!empty($data['caseVerifications'])) && (!empty($data['caseVerificationTables'])) && (!empty($data['caseVerificationTablesAlias'])) && (!empty($data['caseVerificationWheres'])) && (!empty($data['caseVerificationParameters'])) && (!empty($data['caseVerificationValues'])) && (!empty($data['caseVerificationValueTables'])) && (!empty($data['caseVerificationValueTablesAlias'])) && (!empty($data['caseVerificationValueWheres'])) && (!empty($data['caseVerificationElse'])) && (!empty($data['caseVerificationAlias'])) ) {
                                    echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']), strval($data['where']), $data['caseVerifications'], $data['caseVerificationTables'], $data['caseVerificationTablesAlias'], $data['caseVerificationWheres'], $data['caseVerificationParameters'], $data['caseVerificationValues'], $data['caseVerificationValueTables'], $data['caseVerificationValueTablesAlias'], $data['caseVerificationValueWheres'], $data['caseVerificationElse'], $data['caseVerificationAlias'] ) ) );
                                } else {
                                    echo(json_encode(array()));
                                }
                            }                    
                        } else if ( (count($data['type']) == 2) && ($data['type'][0] == "TooComplex") ) {
                            echo( json_encode( SelectSql( $data['type'] ) ) );
                        } else if ( (count($data['type']) == 2) && ( $data['type'][0] == "SimpleWhere" ) ) {
                                if ( (isset($data['innerCamps'])) && (isset($data['innerCampsAlias'])) && (isset($data['innerTables'])) && (isset($data['foreignKey'])) && (!empty($data['where'])) ) {
                                    echo( json_encode( SelectSql( $data['type'], strval($data['table']), strval($data['code']), $data['camps'], $data['campsAlias'], $data['innerCamps'], $data['innerCampsAlias'], $data['innerTables'], strval($data['foreignKey']), strval($data['where']) ) ) );
                                } else {
                                    echo(json_encode(array()));
                                }
                        } else {
                            echo(json_encode(array()));
                        }
                    } else {
                        echo(json_encode(array("broken"=>"broken")));
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