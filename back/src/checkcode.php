<?php
    declare(strict_types=1);
    function checkvaliditycode(string $code, string $table): bool {
        require "connect-localhost.php";
        require_once "safedecrypto.php";
        $sql = "SELECT code FROM ".$table." WHERE code = '".$code."';";
        try {
            $prep = $conn->prepare($sql);
            $prep->execute();
            if ($prep->rowCount() > 0) {
                $conn = null;
                return (true);
            }
            $conn = null;
            return (false);
        } catch(PDOException $e) {
            error_log($sql . "<br>" . $e->getMessage());
            $conn = null;
            return (false);
        }
    }
?>