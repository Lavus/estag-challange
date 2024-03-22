<?php
    declare(strict_types=1);
    function SelectSql(string $type, string $table, string $code = '0', array $camps = [], array $innerCamps = [], array $innerCampsAlias = [], string $innerTable = 'none', string $foreignKey = '0', string $where = '1=0', array $caseVerifications =[], array $caseVerificationTables =[], array $caseVerificationTablesAlias =[], array $caseVerificationWheres =[], array $caseVerificationParameters =[], array $caseVerificationValues =[], array $caseVerificationValueTables =[], array $caseVerificationValueTablesAlias =[], array $caseVerificationValueWheres =[], array $caseVerificationElse =[], array $caseVerificationAlias =[] ): array {
        require_once "ConnectLocalHost.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        require_once __DIR__."/../security/CheckValidityCamp.php";
        require_once "Delete.php";
        require_once "functions/GenerateStringSql.php";
        $connection  = ConnectLocalHost();
        $stringCamps = GenerateStringSql($table,$camps,$camps);
        $stringInnerCamps = GenerateStringSql($innerTable,$innerCamps,$innerCampsAlias);
        $stringCampsSql = $stringCamps.",".$stringInnerCamps;
        $stringCampsSql = rtrim($stringCampsSql, ",");
        $campsSql = array_merge($camps,$innerCampsAlias);
        if ($type == "FullSimple"){
            $sql = "SELECT ".$stringCampsSql." FROM ".$table." ORDER BY ".$table.".code;";
        } else if ($type == "SingleSimple"){
            $sql = "SELECT ".$stringCampsSql." FROM ".$table." Where code = ".$code." ORDER BY ".$table.".code;";
        } else if ($type == "SimpleForeign"){
            $sql = "SELECT ".$stringCampsSql." FROM ".$table.", ".$innerTable." WHERE ".$table.".".$foreignKey." = ".$innerTable.".code ORDER BY ".$table.".code;";
        } else if ($type == "FullCases"){


            // 'caseVerifications' : ['product_code','product_code','product_code','product_code'],
            // 'caseVerificationTables' : ['order_item','order_item','order_item','order_item'],
            // 'caseVerificationTablesAlias' : ['order_item1','order_item2','order_item3','order_item4',],
            // 'caseVerificationWheres' : ['order_item1.code = order_item.code','order_item2.code = order_item.code','order_item3.code = order_item.code','order_item4.code = order_item.code'],
            // 'caseVerificationParameters' : ['IS NOT NULL','IS NOT NULL','IS NOT NULL','IS NOT NULL'],
            // 'caseVerificationValues' : ['name','amount','price','tax'],
            // 'caseVerificationValueTables' : [['products'],['products'],['products'],['categories','products']],
            // 'caseVerificationValueTablesAlias' : [['products1'],['products2'],['products3'],['categories1','products4']],
            // 'caseVerificationValueWheres' : ['products1.code = order_item.product_code','products2.code = order_item.product_code','products3.code = order_item.product_code','products4.code = order_item.product_code AND categories1.code = products4.category_code'],
            // 'caseVerificationElse' : ['False','False','False','False'],
            // 'caseVerificationAlias' : ['products_name','products_amount','products_price','categories_tax']
            // CASE WHEN ( SELECT order_item4.product_code FROM order_item AS order_item4 WHERE order_item4.code = order_item.code ) IS NOT NULL THEN ( SELECT categories1.tax FROM products AS products4, categories AS categories1 WHERE products4.code = order_item.product_code AND categories1.code = products4.category_code ) ELSE 'False' END AS categories_tax,
        
            $stringCaseSql = "";
            foreach($caseVerifications as $index => $campVerification) {
                $stringCaseSql  .= "CASE WHEN ( SELECT ".$caseVerificationTablesAlias[$index].".".$campVerification." FROM ".$caseVerificationTables[$index]." AS ".$caseVerificationTablesAlias[$index]." WHERE ".$caseVerificationWheres[$index]." ) ".$caseVerificationParameters[$index]." THEN ( SELECT ".$caseVerificationValueTablesAlias[$index][0].".".$caseVerificationValues[$index]." FROM ";
                $stringCaseTablesThen = "";
                foreach($caseVerifications as $index => $campVerification) {
                }
            }
            $stringsqlt  = rtrim($stringCamps, ",");

            $sql = "SELECT ".$stringCampsSql." FROM ".$table." ORDER BY ".$table.".code;";
        }
        $data = array();
        error_log($sql);
        try {
            $prepare = $connection ->prepare($sql);
            $prepare->execute();
            if ($prepare->rowCount() > 0) {
                $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
                foreach($result as $row) {
                    $deleted = FALSE;
                    $temporary = array();
                    foreach($campsSql as $camp) {
                        if ( str_contains($camp,"code") ){
                            $temporary[$camp] = $row[$camp];
                        } else {
                            $item = SafeCrypto($row[$camp],"Decrypt");
                            $decoded_item = html_entity_decode($item);
                            if ( CheckValidityCamp($item,$decoded_item,$camp) ){
                                if ( ($type == "FullSimple") || ($type == "SingleSimple") || ($type == "SimpleForeign") ) {
                                    if ( ( $camp == "value_total" ) || ( $camp == "value_tax" ) || ( str_contains($camp,"price") ) ) {
                                        $item = $item = SafeCrypto("$".number_format(floatval($decoded_item), 2, '.', ''),'Html');
                                    } else if ( str_contains($camp,"tax") ) {
                                        $item = $item.'%';
                                    }
                                }
                                $temporary[$camp] = $item;
                            } else {
                                DeleteSql('simple',$table,$row['code']);
                                $deleted = TRUE;
                            }
                        }
                    }
                    if ($deleted == FALSE){
                        $data[$row['code']] = $temporary;
                    }
                }
            }
        } catch(PDOException $e) {
            error_log($sql . "<br>" . $e->getMessage());
        }
        return ($data);
    }
?>