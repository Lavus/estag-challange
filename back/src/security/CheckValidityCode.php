<?php
    declare(strict_types=1);
    function CheckValidityCode(string $code, string $table): bool {
        require_once __DIR__."/../sql/ConnectLocalHost.php";
        $connection  = ConnectLocalHost();
        $sql = "SELECT code FROM ".$table." WHERE code = '".$code."';";
        try {
            $prep = $connection->prepare($sql);
            $prep->execute();
            if ($prep->rowCount() > 0) {
                $connection = null;
                return (true);
            }
            $connection = null;
            return (false);
        } catch(PDOException $e) {
            error_log($sql . "<br>" . $e->getMessage());
            $connection = null;
            return (false);
        }
    }
?>