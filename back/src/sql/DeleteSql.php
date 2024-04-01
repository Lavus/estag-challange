<?php
    declare(strict_types=1);
    function DeleteSql(string $type, string $table, string $code = '0', array $foreignTables = [], array $foreignKeys = [], string $where = '1=0'): bool {
        require_once "ConnectLocalHost.php";
        if ($type == "Simple") {
            $sql_delete = "DELETE FROM ".$table." WHERE code = '".$code."';";
        } else if ($type == "SimpleWhere") {
            $sql_delete = "DELETE FROM ".$table." WHERE '".$where;
        } else {
            return (FALSE);
        }
        $connection  = ConnectLocalHost();
        try {
            $connection ->beginTransaction();
            $connection ->exec($sql_delete);
            $connection ->commit();
        } catch(PDOException $e) {
            $connection ->rollback();
            error_log("Error: " . $e->getMessage() . "<br><br>");
            $connection  = null;
            return (FALSE);
        }
        $connection  = null;
        return (TRUE);
    }
?>