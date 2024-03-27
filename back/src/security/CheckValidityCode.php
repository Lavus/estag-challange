<?php
    declare(strict_types=1);
    function CheckValidityCode(string $code, string $table, string $where = ""): bool {
        if ($where == "") {
            $where = $table.".code = ".$code." ORDER BY ".$table.".code;";
        }
        require_once __DIR__."/../sql/SelectSql.php";
        $type = ['SimpleWhere'];
        $camps = [['code']];
        $campsAlias = ['code'];
        $innerCamps = [];
        $innerCampsAlias = [];
        $innerTables = [];
        $foreignKey = 'none';
        $resultSelect = SelectSql($type,$table,$code,$camps,$campsAlias,$innerCamps,$innerCampsAlias,$innerTables,$foreignKey,$where);
        if (count($resultSelect) > 0){
            return(TRUE);
        } else {
            return(FALSE);
        }
    }
?>