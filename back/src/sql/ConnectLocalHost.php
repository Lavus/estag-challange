<?php
    declare(strict_types=1);
    function ConnectLocalHost(){
        $servername = "pgsql_desafio";
        $username = "root";
        $password = "root";
        $db = "applicationphp";
        try {
            $conn = new PDO("pgsql:host=$servername;dbname=$db", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return ($conn);
        } catch(PDOException $e) {
            error_log("Connection failed: " . $e->getMessage() . "<br><br>");
            $error = $e->getMessage();
            return ($error);
        }
    }
?>