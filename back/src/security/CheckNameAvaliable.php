<?php
    declare(strict_types=1);
    function CheckNameAvaliable(string $name, string $table, string $code = '0'): bool {
        require_once __DIR__."/../sql/SelectSql.php";
        $type = ['SimpleWhere'];
        $camps = [['code'],['name']];
        $campsAlias = ['code','name'];
        $innerCamps = [];
        $innerCampsAlias = [];
        $innerTables = [];
        $foreignKey = 'none';
        $where = $table.".code <> '".$code."';";
        $resultSelect = SelectSql($type,$table,$code,$camps,$campsAlias,$innerCamps,$innerCampsAlias,$innerTables,$foreignKey,$where);
        foreach($resultSelect as $row) {
            $decodeName = html_entity_decode($row['name']);
            if($decodeName == $name){
                return (false);
            }
        }
        return (true);
    }
?>