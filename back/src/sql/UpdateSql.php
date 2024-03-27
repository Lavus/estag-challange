<?php
    declare(strict_types=1);
    function UpdateSql(string $table = 'none', array $camps = [], array $campsAlias = [], array $values = [], array $oldValues = [], string $code = '0'): bool {
        require_once "ConnectLocalHost.php";
        require_once "SelectSql.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        $type = ['SingleSimpleNone'];
        $resultSelect = SelectSql($type,$table,$code,$camps,$campsAlias);
        $continue = TRUE;
        $index = 0;
        $sql = "UPDATE ".$table." SET ";
        foreach($resultSelect[$code] as $indexRow => $row) {
            if ($indexRow != 'code'){
                if ($row != $oldValues[$index]){
                    $continue = FALSE;
                } else if ($row != $values[$index]){
                    $setValue = SafeCrypto($values[$index],'Encrypt');
                    $sql .= $campsAlias[$index+1]." = '".$setValue."',";
                }
                $index ++;
            }
        }
        $sql = rtrim($sql, ",");
        $sql .= " WHERE ".$table.".code = '".$code."';";
        // error_log($sql);
        if ($continue){
            $connection  = ConnectLocalHost();
            try {
                $connection->beginTransaction();
                $connection->exec($sql);
                $connection->commit();
            } catch(PDOException $e) {
                $connection->rollback();
                error_log("Error: " . $e->getMessage() . "<br><br>");
                $connection = null;
                return (FALSE);
            }
            $connection = null;
            return (TRUE);
        } else {
            return (FALSE);
        }
    }
?>