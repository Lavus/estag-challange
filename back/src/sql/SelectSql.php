<?php
    declare(strict_types=1);
    function SelectSql(array $type = [], string $table = 'none', string $code = '0', array $camps = [], array $campsAlias = [], array $innerCamps = [], array $innerCampsAlias = [], array $innerTables = [], string $foreignKey = 'none', string $where = '1=0', array $caseVerifications = [], array $caseVerificationTables =[], array $caseVerificationTablesAlias =[], array $caseVerificationWheres =[], array $caseVerificationParameters =[], array $caseVerificationValues =[], array $caseVerificationValueTables =[], array $caseVerificationValueTablesAlias =[], array $caseVerificationValueWheres =[], array $caseVerificationElse =[], array $caseVerificationAlias =[] ): array {
        require_once "ConnectLocalHost.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        require_once __DIR__."/../security/CheckValidityCamp.php";
        require_once "DeleteSql.php";
        require_once "functions/GenerateStringCampsSql.php";
        require_once "functions/GenerateStringTablesSql.php";
        require_once "functions/GenerateFullCasesHome.php";
        $connection  = ConnectLocalHost();
        if ( (count($type) == 1) || ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") ) ) {
            $stringCamps = GenerateStringCampsSql(array($table),array($camps),array($campsAlias));
            $stringInnerCamps = GenerateStringCampsSql($innerTables,$innerCamps,$innerCampsAlias);
            $stringCampsSql = $stringCamps.",".$stringInnerCamps;
            $stringCampsSql = rtrim($stringCampsSql, ",");
            $campsSql = $campsAlias;
            foreach($innerCampsAlias as $indexCampsAlias => $innerAlias) {
                $campsSql = array_merge($campsSql,$innerAlias);
            }
            $stringTablesSql = $table.",".GenerateStringTablesSql($innerTables);
            $stringTablesSql = rtrim($stringTablesSql, ",");
            if ($type[0] == "FullSimple"){
                $sql = "SELECT ".$stringCampsSql." FROM ".$table." ORDER BY ".$table.".code;";
            } else if ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") && ($code != '0') ) {
                $sql = "SELECT ".$stringCampsSql." FROM ".$stringTablesSql." WHERE ".$table.".".$foreignKey." = ".$innerTables[0].".code AND ".$innerTables[0].".code = ".$code."  ORDER BY ".$table.".code;";
            } else if ($type[0] == "SimpleWhere"){
                $sql = "SELECT ".$stringCampsSql." FROM ".$stringTablesSql." WHERE ".$where;
            } else if ( ($type[0] == "SingleSimple") || ($type[0] == "SingleSimpleNone") ) {
                $sql = "SELECT ".$stringCampsSql." FROM ".$table." Where ".$table.".code = ".$code." ORDER BY ".$table.".code;";
            } else if ($type[0] == "SimpleForeign"){
                $sql = "SELECT ".$stringCampsSql." FROM ".$stringTablesSql." WHERE ".$table.".".$foreignKey." = ".$innerTables[0].".code ORDER BY ".$table.".code;";
            } else if ( ($type[0] == "FullCases") || ($type[0] == "FullCasesHome") ){            
                $stringCaseSql = "";
                for ($index = 0; $index < count($caseVerifications); $index++){
                    $caseSelect = "SELECT ".GenerateStringCampsSql($caseVerificationTablesAlias[$index],array($caseVerifications[$index]),$caseVerifications[$index]);
                    $caseFrom = "FROM ".GenerateStringTablesSql($caseVerificationTables[$index],$caseVerificationTablesAlias[$index]);
                    $caseWhere = "WHERE ".$caseVerificationWheres[$index];
                    $caseParameter = $caseVerificationParameters[$index];
                    $thenSelect = "SELECT ".GenerateStringCampsSql($caseVerificationValueTablesAlias[$index],array($caseVerificationValues[$index]),$caseVerificationValues[$index]);
                    $thenFrom = "FROM ".GenerateStringTablesSql($caseVerificationValueTables[$index],$caseVerificationValueTablesAlias[$index]);
                    $thenWhere = "WHERE ".$caseVerificationValueWheres[$index];
                    $caseElse = "ELSE '".$caseVerificationElse[$index]."'";
                    $caseAlias = "AS ".$caseVerificationAlias[$index].",";
                    $stringCaseSql  .= "CASE WHEN ( ".$caseSelect." ".$caseFrom." ".$caseWhere." ) ".$caseParameter." THEN ( ".$thenSelect." ".$thenFrom." ".$thenWhere." ) ".$caseElse." END ".$caseAlias;
                }
                $stringCaseSql  = rtrim($stringCaseSql, ",");
                $stringCampsSql = $stringCampsSql.",".$stringCaseSql;
                $stringCampsSql = rtrim($stringCampsSql, ",");
                if ($type[0] == "FullCases"){
                    $campsSql = array_merge($campsSql,$caseVerificationAlias);
                }
                $sql = "SELECT ".$stringCampsSql." FROM ".$stringTablesSql." WHERE ".$where;
            }
        } else {
            $sql = $type[1];
        }
        $data = array();
        $cases = array('False','0',0);
        // error_log($sql);
        try {
            $prepare = $connection ->prepare($sql);
            $prepare->execute();
            if ($prepare->rowCount() > 0) {
                $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
                foreach($result as $row) {
                    $deleted = FALSE;
                    $temporary = array();
                    if ( $type[0] == "FullCasesHome" ) {
                        $resultTemporary = GenerateFullCasesHome($row);
                        $deleted = $resultTemporary[0];
                        $temporary = $resultTemporary[1];
                    } else {
                        foreach($campsSql as $camp) {
                            if ( str_contains($camp,"code") ){
                                if ( ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") ) && ( ($camp == 'order_code') || ($camp == 'value_total') || ($camp == 'value_tax') ) ){
                                    $temporary['orders'][$camp] = $row[$camp];
                                } else if ( ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") ) ){
                                    $temporary['order_item'][$camp] = $row[$camp];
                                } else {
                                    $temporary[$camp] = $row[$camp];
                                }
                            } else if (!in_array($row[$camp],$cases)) {
                                $item = SafeCrypto($row[$camp],"Decrypt");
                                $decoded_item = html_entity_decode($item);
                                if ( CheckValidityCamp($item,$decoded_item,$camp) ){
                                    if ( !(str_contains($camp,"_nope")) ) {
                                        if ( ($type[0] == "FullSimple") || ($type[0] == "SingleSimple") || ($type[0] == "SimpleForeign") || ($type[0] == "FullCases") || ($type[0] == "SimpleWhere") ) {
                                            if ( ( $camp == "value_total" ) || ( $camp == "value_tax" ) || ( str_contains($camp,"price") ) ) {
                                                $item = SafeCrypto("$".number_format(floatval($decoded_item), 2, '.', ''),'Html');
                                            } else if ( str_contains($camp,"tax") ) {
                                                $item = $item.'%';
                                            }
                                        }
                                    }
                                    if ( ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") ) && ( ($camp == 'order_code') || ($camp == 'value_total') || ($camp == 'value_tax') ) ){
                                        $temporary['orders'][$camp] = $item;
                                    } else if ( ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") ) ){
                                        $temporary['order_item'][$camp] = $item;
                                    } else {
                                        $temporary[$camp] = $item;
                                    }
                                } else {
                                    DeleteSql('simple',$table,$row['code']);//need work
                                    $deleted = TRUE;
                                }
                            } else {
                                $temporary[$camp] = $row[$camp];
                            }
                        }
                    }
                    if ($deleted == FALSE){
                        if ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") ){
                            $temporary['order_item']['total'] = (intval(html_entity_decode(SafeCrypto($row['amount'],"Decrypt")))*floatval(html_entity_decode(SafeCrypto($row['amount'],"Decrypt"))));
                            $temporary['order_item']['total'] = SafeCrypto("$".number_format(floatval($temporary['order_item']['total']), 2, '.', ''),'Html');
                            $data['orders'][0] = $temporary['orders'];
                            $data['rows'][$row['code']] = $temporary['order_item'];
                        } else {
                            $data[$row['code']] = $temporary;
                        }
                    }
                }
            }
        } catch(PDOException $e) {
            error_log($sql . "<br>" . $e->getMessage());
        }
        return ($data);
    }
?>