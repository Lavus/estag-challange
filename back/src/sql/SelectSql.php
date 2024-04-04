<?php
    declare(strict_types=1);
    function SelectSql(array $type = [], string $table = 'none', string $code = '0', array $camps = [], array $campsAlias = [], array $innerCamps = [], array $innerCampsAlias = [], array $innerTables = [], string $foreignKey = 'none', string $where = '1=0', array $caseVerifications = [], array $caseVerificationTables =[], array $caseVerificationTablesAlias =[], array $caseVerificationWheres =[], array $caseVerificationParameters =[], array $caseVerificationValues =[], array $caseVerificationValueTables =[], array $caseVerificationValueTablesAlias =[], array $caseVerificationValueWheres =[], array $caseVerificationElse =[], array $caseVerificationAlias =[] ): array {
        require_once "ConnectLocalHost.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        require_once __DIR__."/../security/CheckValidityCamp.php";
        require_once "DeleteSql.php";
        require_once "UpdateSql.php";
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
        $deletedTrue = FALSE;
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
                        $totalValues = $resultTemporary[2];
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
                                    if (in_array($camp,$campsAlias)){
                                        DeleteSql(['Simple','Broken'],$table,strval($row['code']));
                                    } else if (in_array($camp,$caseVerificationAlias)){
                                        $deleted = TRUE;
                                    } else {
                                        foreach($innerCampsAlias as $indexCampsAlias => $innerAlias) {
                                            if (in_array($camp,$innerAlias)){
                                                if ($foreignKey != 'none'){
                                                    $foreign = $foreignKey;
                                                } else if ($table == 'products'){
                                                    $foreign = 'category_code';
                                                } else if ($table == 'order_item'){
                                                    if ($innerTables[$indexCampsAlias] == 'products'){
                                                        $foreign = 'product_code';
                                                    }else if ($innerTables[$indexCampsAlias] == 'orders'){
                                                            $foreign = 'order_code';
                                                    }
                                                }
                                                DeleteSql(['SimpleForeign','Broken'],$table,strval($row['code']),array($innerTables[$indexCampsAlias]),array($foreign));
                                            }
                                        }
                                    }
                                    $deleted = TRUE;
                                }
                            } else {
                                $temporary[$camp] = $row[$camp];
                            }
                        }
                    }
                    if ($deleted == FALSE){
                        if ( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") ){
                            $temporary['order_item']['total'] = (intval(html_entity_decode(SafeCrypto($row['amount'],"Decrypt")))*floatval(html_entity_decode(SafeCrypto($row['price'],"Decrypt"))));
                            $temporary['order_item']['total'] = SafeCrypto("$".number_format(floatval($temporary['order_item']['total']), 2, '.', ''),'Html');
                            $data['orders'][0] = $temporary['orders'];
                            $data['rows'][$row['code']] = $temporary['order_item'];
                        } else if ( $type[0] == "FullCasesHome" ) {
                            if (!(isset($data['totalValues'][0]))){
                                $data['totalValues'][0] = $totalValues;
                            } else {
                                $data['totalValues'][0]['value_total'] += $totalValues['value_total'];
                                $data['totalValues'][0]['value_tax'] += $totalValues['value_tax'];
                                if (isset($totalValues['broken'])){
                                    $data['totalValues'][0]['broken'] = 'TRUE';
                                }
                            }
                            $data['rows'][$row['code']] = $temporary;
                        } else {
                            $data[$row['code']] = $temporary;
                        }
                    } else {
                        $deletedTrue = TRUE;
                    }
                }
            }
        } catch(PDOException $e) {
            error_log($sql . "<br>" . $e->getMessage());
        }
        if ( ( $type[0] == "FullCasesHome" ) && (isset($data['totalValues'])) ) {
            $data['totalValues'][0]['value_total'] = round(($data['totalValues'][0]['value_total']),2);
            $data['totalValues'][0]['value_tax'] = round(($data['totalValues'][0]['value_tax']),2);
            if (!(isset($data['totalValues'][0]['broken']))){
                $data['totalValues'][0]['broken'] = 'FALSE';
            }
            $orderSelectValues = SelectSql(
                ['SimpleWhere'],
                'orders',
                '0',
                [['code'],['value_total'],['value_tax']],
                ['code','value_total_nope','value_tax_nope'],
                [],
                [],
                [],
                'none',
                'orders.code IN ( SELECT MAX( orders1.code ) FROM orders as orders1 );'
            );
            $orderCode = strval(array_keys($orderSelectValues)[0]);
            $valueTotal = $orderSelectValues[$orderCode]['value_total_nope'];
            $valueTax = $orderSelectValues[$orderCode]['value_tax_nope'];
            $decodeValueTotal = html_entity_decode($valueTotal);
            $decodeValueTax = html_entity_decode($valueTax);
            if ( ( round(floatval($data['totalValues'][0]['value_total']),2) != round(floatval($decodeValueTotal),2) ) || ( round(floatval($data['totalValues'][0]['value_tax']),2) != round(floatval($decodeValueTax),2) ) ) {
                error_log('Ocorreu um erro no valor da orders, valor atualizado');
                UpdateSql(
                    ['orders'],
                    [['code'],['value_total'],['value_tax']],
                    ['code','value_total','value_tax'],
                    [SafeCrypto(strval($data['totalValues'][0]['value_total']),'Html'),SafeCrypto(strval($data['totalValues'][0]['value_tax']),'Html')],
                    [$valueTotal,$valueTax],
                    $orderCode
                );
            }
            $data['totalValues'][0]['value_total'] = SafeCrypto("$".number_format(($data['totalValues'][0]['value_total']), 2, '.', ''),'Html');;
            $data['totalValues'][0]['value_tax'] = SafeCrypto("$".number_format(($data['totalValues'][0]['value_tax']), 2, '.', ''),'Html');;
        }
        if (($deletedTrue)&&( (count($type) == 2) && ($type[0] == "SimpleWhere") && ($type[1] == "tableview") )){
            $data=array();
        }
        return ($data);
    }
?>