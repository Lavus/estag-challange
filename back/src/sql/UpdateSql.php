<?php
    declare(strict_types=1);
    function UpdateSql(array $table = [], array $camps = [], array $campsAlias = [], array $values = [], array $oldValues = [], string $code = '0'): array {
        require_once "ConnectLocalHost.php";
        require_once "SelectSql.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        $type = ['SingleSimpleNone'];
        $resultSelect = SelectSql($type,$table[0],$code,$camps,$campsAlias);
        $continue = TRUE;
        $index = 0;
        $sql = "UPDATE ".$table[0]." SET ";
        if (count($resultSelect) == 0){
            return ([FALSE]);
        }
        foreach($resultSelect[$code] as $indexRow => $row) {
            if ($indexRow != 'code'){
                if ($row != $oldValues[$index]){
                    $continue = FALSE;
                } else if ($row != $values[$index]){
                    if ( str_contains($indexRow,"code") ){
                        $setValue = $values[$index];
                    } else {
                        $setValue = SafeCrypto($values[$index],'Encrypt');
                    }
                    $sql .= $campsAlias[$index+1]." = '".$setValue."',";
                }
                $index ++;
            }
        }
        $sql = rtrim($sql, ",");
        $sql .= " WHERE ".$table[0].".code = '".$code."';";
        // error_log($sql);
        if ($continue){
            $connection  = ConnectLocalHost();
            try {
                $connection->beginTransaction();
                $connection->exec($sql);
                if (count($table) == 2){
                    return ([TRUE,$connection]);
                }
                $connection->commit();
            } catch(PDOException $e) {
                $connection->rollback();
                error_log("Error: " . $e->getMessage() . "<br><br>");
                $connection = null;
                return ([FALSE]);
            }
            $connection = null;
            return ([TRUE]);
        } else {
            return ([FALSE]);
        }
    }
?>