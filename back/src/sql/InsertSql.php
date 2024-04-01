<?php
    declare(strict_types=1);
    function InsertSql(string $table = 'none', array $camps = [], array $values = []): bool {
        require_once "ConnectLocalHost.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        $stringCamps = "";
        $stringValues = "";
        foreach($camps as $indexCamp => $camp) {
            $stringCamps .= $camp.",";
            if ( str_contains($camp,"code") ){
                $stringValues .= "'".$values[$indexCamp]."',";
            } else {
                $stringValues .= "'".SafeCrypto($values[$indexCamp],'Encrypt')."',";
            }
        }
        $stringCamps = rtrim($stringCamps, ",");
        $stringValues = rtrim($stringValues, ",");
        $sql = "INSERT INTO ".$table." ( ".$stringCamps." ) VALUES ( ".$stringValues." );";
        error_log($sql);
        $connection  = ConnectLocalHost();
        // try {
        //     $connection->beginTransaction();
        //     $connection->exec($sql);
        //     $connection->commit();
        // } catch(PDOException $e) {
        //     $connection->rollback();
        //     error_log("Error: " . $e->getMessage() . "<br><br>");
        //     $connection = null;
        //     return (FALSE);
        // }
        $connection = null;
        return (TRUE);
    }
?>