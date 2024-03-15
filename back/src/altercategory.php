<?php
    declare(strict_types=1);
    header("Location: categories.php");
    require_once "checkname.php";
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    if (isset($_POST['tax'])){
        if ( (!empty($_POST['categoryname'])) && ((!empty($_POST['tax'])) || $_POST['tax'] == 0 ) && (!empty($_POST['oldname'])) && (!empty($_POST['oldtax'])) && (!empty($_POST['alterid'])) ){
            $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
            $regexnumbers = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
            if ( (preg_match($regexname, $_POST['categoryname'])) && (preg_match($regexnumbers, $_POST['tax'])) && (intval($_POST['alterid']) !== 0) && ($_POST['tax'] >= 0 ) && (checknameavaliable($_POST['categoryname'],"categories",strval($_POST['alterid']))) && (checkvaliditycode(strval($_POST['alterid']),"categories")) ) {
                require "connect-localhost.php";
                require_once "safedecrypto.php";
                $sqlid = "SELECT categories.name, categories.tax FROM categories WHERE categories.code = '".$_POST['alterid']."';";
                $prepid = $conn->prepare($sqlid);
                $prepid->execute();
                $resultid = $prepid->fetch(PDO::FETCH_ASSOC);
                $tax1 = safeDecrypt($resultid['tax'], getkey());
                $name1 = safeDecrypt($resultid['name'], getkey());
                $decodetax1 = html_entity_decode($tax1);
                $decodename1 = html_entity_decode($name1);
                if ( ($name1 == 'FALSE') || (!(preg_match($regexname, $decodename1))) || ($tax1 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax1))) ){
                    $sqldelete1 = "DELETE FROM categories WHERE categories.code = '".$_POST['alterid']."';";
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqldelete1);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                } else if ( ($decodetax1 == $_POST['oldtax']) && ($decodename1 == $_POST['oldname']) ){
                    if ( ($_POST['categoryname'] != $decodename1) && ($_POST['tax'] != $decodetax1) ){
                        $setname = safeEncrypt(codifyhtml($_POST['categoryname']), getkey());
                        $settax = safeEncrypt(codifyhtml($_POST['tax']), getkey());
                        $sql = "UPDATE categories SET name = '".$setname."', tax = '".$settax."' WHERE code = '".$_POST['alterid']."';";
                    } else if ($_POST['categoryname'] != $decodename1) {
                        $setname = safeEncrypt(codifyhtml($_POST['categoryname']), getkey());
                        $sql = "UPDATE categories SET name = '".$setname."' WHERE code = '".$_POST['alterid']."';";
                    } else if ($_POST['tax'] != $decodetax1) {
                        $settax = safeEncrypt(codifyhtml($_POST['tax']), getkey());
                        $sql = "UPDATE categories SET tax = '".$settax."' WHERE code = '".$_POST['alterid']."';";
                    }
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sql);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                    $conn = null;
                }
            }
        }
    }
?>