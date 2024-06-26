<?php
    declare(strict_types=1);
    function CheckValidityCamp (string $text, string $decode_text, string $camp, string $type = "none"): bool {
        if ( $text == 'FALSE' ){
            return (FALSE);
        } else {
            if ( str_contains($camp,"name") ) {
                $regex = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
                $min = "not";
            } else if ( ( str_contains($camp,"value_total") ) || ( str_contains($camp,"value_tax") ) ) {
                $regex = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
                $min = "0";
            } else if ( str_contains($camp,"amount") ) {
                $regex = "/^[0-9]{1,}$/";
                if ( $type == "none" ) {
                    $min = "0";
                } else {
                    $min = "1";
                }
            } else if ( str_contains($camp,"tax") ) {
                $regex= "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
                $min = "0";
            } else if ( str_contains($camp,"price") ) {
                $regex = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
                $min = "0.01";
            } else {
                return (FALSE);
            }
            if ( preg_match($regex, $decode_text) ) {
                if ( $min == "not" ){
                    return (TRUE);
                } else {
                    if ( floatval($decode_text) >= floatval($min) ){
                        return (TRUE);
                    } else {
                        return (FALSE);
                    }
                }
            } else {
                return (FALSE);
            }
        }
    }
?>