<?php
    declare(strict_types=1);
    function SelectSql(array $type = [], string $table = 'none', string $code = '0', array $camps = [], array $campsAlias = [], array $innerCamps = [], array $innerCampsAlias = [], array $innerTables = [], string $foreignKey = '0', string $where = '1=0', array $caseVerifications = [], array $caseVerificationTables =[], array $caseVerificationTablesAlias =[], array $caseVerificationWheres =[], array $caseVerificationParameters =[], array $caseVerificationValues =[], array $caseVerificationValueTables =[], array $caseVerificationValueTablesAlias =[], array $caseVerificationValueWheres =[], array $caseVerificationElse =[], array $caseVerificationAlias =[] ): array {
        require_once "ConnectLocalHost.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        require_once __DIR__."/../security/CheckValidityCamp.php";
        require_once "Delete.php";
        require_once "functions/GenerateStringCampsSql.php";
        require_once "functions/GenerateStringTablesSql.php";
        require_once "functions/GenerateFullCasesHome.php";
        $connection  = ConnectLocalHost();
        if ( count($type) == 1 ) {
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
            } else if ($type[0] == "SimpleWhere"){
                $sql = "SELECT ".$stringCampsSql." FROM ".$stringTablesSql." WHERE ".$where;
            } else if ($type[0] == "SingleSimple"){
                $sql = "SELECT ".$stringCampsSql." FROM ".$table." Where code = ".$code." ORDER BY ".$table.".code;";
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
                                $temporary[$camp] = $row[$camp];
                            } else {
                                $item = SafeCrypto($row[$camp],"Decrypt");
                                $decoded_item = html_entity_decode($item);
                                if ( CheckValidityCamp($item,$decoded_item,$camp) ){
                                    if ( ($type[0] == "FullSimple") || ($type[0] == "SingleSimple") || ($type[0] == "SimpleForeign") || ($type[0] == "FullCases") || ($type[0] == "SimpleWhere") ) {
                                        if ( ( $camp == "value_total" ) || ( $camp == "value_tax" ) || ( str_contains($camp,"price") ) ) {
                                            $item = SafeCrypto("$".number_format(floatval($decoded_item), 2, '.', ''),'Html');
                                        } else if ( str_contains($camp,"tax") ) {
                                            $item = $item.'%';
                                        }
                                    }
                                    $temporary[$camp] = $item;
                                } else {
                                    DeleteSql('simple',$table,$row['code']);//need work
                                    $deleted = TRUE;
                                }
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