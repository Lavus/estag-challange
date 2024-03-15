<?php
    declare(strict_types=1);
    require "connect-localhost.php";
    require_once "safedecrypto.php";
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    $executesecure = TRUE;
    if ( (!empty($_POST['deletekey'])) ) {
        if (intval($_POST['deletekey']) !== 0){
            if ( checkvaliditycode(strval($_POST['deletekey']),'categories') ){
                $statement1 = $conn->query("SELECT categories.name, CASE WHEN ( SELECT products.code FROM products WHERE products.category_code = categories.code LIMIT 1) IS NOT NULL THEN ( SELECT COUNT(products1.code) FROM products AS products1 WHERE products1.category_code = categories.code  ) ELSE 0 END AS count_products FROM categories WHERE categories.code = '".$_POST['deletekey']."';");
                $data = $statement1->fetch();
                echo("<form action='deletecategory.php' method='post' id='confirmfromdeletecategory'></form>");
                $name1 = safeDecrypt($data['name'], getkey());
                $decodename1 = html_entity_decode($name1);
                if ( ($name1 == 'FALSE') || (!(preg_match($regexname, $decodename1))) ) {
                    $executesecure = FALSE;
                    $sqldelete1 = "DELETE FROM categories WHERE code = '".$_POST['deletekey']."';";
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqldelete1);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                    echo("<div class='refreshthepagenowcategories'></div>");
                }
                if ($executesecure){
                    echo("<div id='alert'><div id='textalert'>Do you really want to delete the category '".$name1."' ?<br>");
                    if ($data['count_products'] > 0){
                        echo ("There's ".$data['count_products']." product(s) within this category '".$name1."', by deleting the category, those products will be deleted together, do you really want to continue?");
                    }
                    echo ("</div><button type='submit' name='deleteconfirmedkey' form='confirmfromdeletecategory' value='".$_POST['deletekey']."' id='yesbutton'>YES</button><button id='nobutton'>NO 10s</button></div>");
                } else {
                    echo("<div class='refreshthepagenowcategories'></div>");
                }
            }
        }
    }
    $alterkey = FALSE;
    if ( (!empty($_POST['alterkey'])) ) {
        if (intval($_POST['alterkey']) !== 0){
            if ( checkvaliditycode(strval($_POST['alterkey']),'categories') ){
                $alterkey = TRUE;
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <p id="test" hidden></p>
    <header>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="store.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous"></script>
        <script src="store.js"></script>
        <title>Suite Store</title>
    </header>
    <body>
        <div class="topnav" id="myTopnav">
            <a id="selectedpage" href="index.php">Suite Store</a>
            <a href="products.php">Products</a>
            <a href="categories.php">Categories</a>
            <a href="history.php">History</a>
            <a href="#" class="icon">&#9776;</a>
        </div>
        <div class="main">
            <?php
            if ($alterkey){
                $statement2 = $conn->query("SELECT categories.name, categories.tax  FROM categories WHERE categories.code = '".$_POST['alterkey']."';");
                $data1 = $statement2->fetch();
                $name3 = safeDecrypt($data1['name'], getkey());
                $tax3 = safeDecrypt($data1['tax'], getkey());
                $decodename3 = html_entity_decode($name3);
                $decodetax3 = html_entity_decode($tax3);
                if ( ($name3 == 'FALSE') || (!(preg_match($regexname, $decodename3))) || ($tax3 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax3))) ){
                    $sqldelete2 = "DELETE FROM categories WHERE code = '".$_POST['alterkey']."';";
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqldelete2);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                    echo("<div class='refreshthepagenowcategories'></div>");
                } else {
                    echo("<div class='textdropleft'><div title='View insert category'>View alter category</div><div class='icon hidden'>&#9776;</div></div>");
                    echo("<div class='textdropright'><div title='View Categories'>View Categories</div><div class='icon'>&#9776;</div></div>");
                    echo("<div class='left show'>");
                    echo("<form id='formaltercategories' action='altercategory.php' method='post'>");
                    echo("<input type='hidden' id='alterid' name='alterid' value='".$_POST['alterkey']."'>");
                    echo("<input type='hidden' id='oldname' name='oldname' value='".$decodename3."'>");
                    echo("<input type='text' id='categoryname' value='".$decodename3."' name='categoryname' placeholder='Category name'  class='half' maxlength='255' title='Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.' pattern='^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$' required>");
                    echo("<input type='hidden' id='oldtax' name='oldtax' value='".$decodetax3."'>");
                    echo("<input type='number' id='tax' value='".$decodetax3."' name='tax' step='0.01' min='0' max='9999.99' placeholder='Tax' class='half' required>");
                    echo("<input type='submit' value='Alter Category' class='bluebold full'>");
                    echo("</form>");
                    echo("</div>");
                    echo("<div class='right'>");
                }
            } else {
                echo("<div class='textdropleft'><div title='View insert category'>View insert category</div><div class='icon'>&#9776;</div></div>");
                echo("<div class='textdropright'><div title='View Categories'>View Categories</div><div class='icon hidden'>&#9776;</div></div>");
                echo("<div class='left'>");
                echo("<form id='formcategories' action='addcategory.php' method='post'>");
                echo("<input type='text' id='categoryname' name='categoryname' placeholder='Category name' class='half' maxlength='255' title='Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.' pattern='^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$' required>");
                echo("<input type='number' id='tax' name='tax' step='0.01' min='0' max='9999.99' placeholder='Tax' class='half' required>");
                echo("<input type='submit' value='Add Category' class='bluebold full'>");
                echo("</form>");
                echo("</div>");
                echo("<div class='right show'>");
            }
            ?> 
                <form action="categories.php" method="post" id="deleteformcategory"></form>
                <form action="categories.php" method="post" id="alterformcategory"></form>
                <div class="scroll">
                    <table id="tablecategories" class="collapse lefttext">
                        <?php
                            $names = ["Code","Category","Tax"];
                            echo("<tr>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<th title='".$names[$index]."'>".codifyhtml($names[$index])."</th>");
                                }
                            echo("</tr>");
                            $sql = "SELECT categories.code, categories.name, categories.tax FROM categories ORDER BY code;";
                            try {
                                $prep = $conn->prepare($sql);
                                $prep->execute();
                                if ($prep->rowCount() > 0) {
                                    $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($result as $row) {
                                        $name2 = safeDecrypt($row['name'], getkey());
                                        $tax2 = safeDecrypt($row['tax'], getkey());
                                        $decodename2 = html_entity_decode($name2);
                                        $decodetax2 = html_entity_decode($tax2);
                                        if ( ($name2 == 'FALSE') || (!(preg_match($regexname, $decodename2))) || ($tax2 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax2))) ){
                                            $executesecure = FALSE;
                                            $sqldelete1 = "DELETE FROM categories WHERE code = '".$row['code']."';";
                                            try {
                                                $conn->beginTransaction();
                                                $conn->exec($sqldelete1);
                                                $conn->commit();
                                            } catch(PDOException $e) {
                                                $conn->rollback();
                                                error_log("Error: " . $e->getMessage() . "<br><br>");
                                            }
                                            echo("<div class='refreshthepagenowcategories'></div>");
                                        }
                                        if ($executesecure){
                                            echo("<tr>");
                                                echo ("<td><div title='".$row['code']."'>".codifyhtml(strval($row['code']))."</div><button type='submit' name='alterkey' form='alterformcategory' class='extrabt alter' value='".$row['code']."'>&#9997;</button></td>");
                                                echo ("<td title='".$decodename2."'>".$name2."</td>");
                                                echo ("<td><div title='".$decodetax2."%'>".$tax2."%</div><button type='submit' name='deletekey' form='deleteformcategory' class='extrabt delete' value='".$row['code']."'>&#128465;</button></td>");
                                            echo("</tr>");
                                        } else {
                                            echo("<div class='refreshthepagenowcategories'></div>");
                                        }
                                    }
                                }
                            } catch(PDOException $e) {
                                error_log($sql . "<br>" . $e->getMessage());
                            }
                            $conn = null;
                            echo("<tr class='last'>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<td></td>");
                                }
                            echo("</tr>");
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>