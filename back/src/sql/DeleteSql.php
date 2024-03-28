<?php
    declare(strict_types=1);
    function DeleteSql(string $type, string $table, string $code, array $foreignTables = [], array $foreignKeys = [], string $where = ''): bool {
        require_once "ConnectLocalHost.php";
        $connection  = ConnectLocalHost();
        if ($type == "Simple") {
            $sql_delete = "DELETE FROM ".$table." WHERE code = '".$code."';";
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
        } else {
            $connection  = null;
            return (FALSE);
        }
    }
?>