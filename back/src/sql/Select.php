<?php
    declare(strict_types=1);
    function SelectSql(string $type, string $table, string $code = '0'): array {
        require_once "ConnectLocalHost.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        require_once __DIR__."/../security/CheckValidityCamp.php";
        require_once "Delete.php";
        $connection  = ConnectLocalHost();
        if ($table == "categories"){
            $list_of_camps = ["code","name","tax"];
        } else if ($table == "products"){
            $list_of_camps = ["code","name","amount","price","category_code"];
        } else if ($table == "orders"){
            $list_of_camps = ["code","value_total","value_tax"];
        } else if ($table == "order_item"){
            $list_of_camps = ["code","order_code","product_code","product_name","amount","price","tax"];
        }
        if ($type == "FullSimple"){
            $sql = "SELECT ".$table.".* FROM ".$table." ORDER BY code;";
        } else if ($type == "SingleSimple"){
            $sql = "SELECT ".$table.".* FROM ".$table." Where code = ".$code.";";
        }
        $data = array();
        try {
            $prepare = $connection ->prepare($sql);
            $prepare->execute();
            if ($prepare->rowCount() > 0) {
                $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
                foreach($result as $row) {
                    $deleted = FALSE;
                    $temporary = array();
                    foreach($list_of_camps as $camp) {
                        if ( str_contains($camp,"code") ){
                            $temporary[$camp] = [$row[$camp],$row[$camp]];
                        } else {
                            $item = SafeCrypto($row[$camp],"Decrypt");
                            $decoded_item = html_entity_decode($item);
                            if ( CheckValidityCamp($item,$decoded_item,$camp) ){
                                $temporary[$camp] = [$item,$decoded_item];
                            } else {
                                DeleteSql('simple',$table,$row['code']);
                                $deleted = TRUE;
                            }
                        }
                    }
                    if ($deleted == FALSE){
                        $data[$row['code']] = $temporary;
                    }
                }
            }
        } catch(PDOException $e) {
            error_log($sql . "<br>" . $e->getMessage());
        }
        return ($data);
    }
?>