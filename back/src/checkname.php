<?php
    declare(strict_types=1);
    function checknameavaliable(string $name, string $table, string $code = '0'): bool {
        require "connect-localhost.php";
        require_once "safedecrypto.php";
        $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
        $sql = "SELECT code, name FROM ".$table." WHERE code <> '".$code."';";
        try {
            $prep = $conn->prepare($sql);
            $prep->execute();
            if ($prep->rowCount() > 0) {
                $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                foreach($result as $row) {
                    $name1 = safeDecrypt($row['name'], getkey());
                    $decodename1 = html_entity_decode($name1);
                    if ( ($name1 == 'FALSE') || (!(preg_match($regexname, $decodename1))) ){
                        $sqldelete1 = "DELETE FROM ".$table." WHERE code = '".$row['code']."';";
                        try {
                            $conn->beginTransaction();
                            $conn->exec($sqldelete1);
                            $conn->commit();
                        } catch(PDOException $e) {
                            $conn->rollback();
                            error_log("Error: " . $e->getMessage() . "<br><br>");
                        }
                        return (false);
                    }else{
                        if($decodename1 == $name){
                            return (false);
                        }
                    }
                }
            }
            $conn = null;
            return (true);
        } catch(PDOException $e) {
            error_log($sql . "<br>" . $e->getMessage());
            $conn = null;
            return (false);
        }
    }
?>