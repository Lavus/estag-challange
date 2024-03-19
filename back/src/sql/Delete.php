<?php
    declare(strict_types=1);
    function DeleteSql(string $type, string $table, string $code): bool {
        require "ConnectLocalHost.php";
        $connection  = ConnectLocalHost();
        if ($type == "simple") {
            $sql_delete = "DELETE FROM ".$table." WHERE code = '".$code."';";
            try {
                $connection ->beginTransaction();
                $connection ->exec($sql_delete);
                $connection ->commit();
                $connection  = null;
                return (TRUE);
            } catch(PDOException $e) {
                $connection ->rollback();
                error_log("Error: " . $e->getMessage() . "<br><br>");
                $connection  = null;
                return (FALSE);
            }
        } else {
            $connection  = null;
            return (FALSE);
        }
    }
?>