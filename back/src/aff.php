<?php
    declare(strict_types=1);
    require_once "connect-localhost.php";
    require_once "safedecrypto.php";
    require_once "checkcode.php";
    require_once "sql/Select.php";
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
                echo("<div class='textdropleft'><div title='View insert category'>View insert category</div><div class='icon'>&#9776;</div></div>");
                echo("<div class='textdropright'><div title='View Categories'>View Categories</div><div class='icon hidden'>&#9776;</div></div>");
                echo("<div class='left'>");
                var_dump(json_encode(SelectSql("FullSimple","categories")));
                echo("</div>");
                echo("<div class='right show'>");
            ?> 
                <form action="categories.php" method="post" id="deleteformcategory"></form>
                <form action="categories.php" method="post" id="alterformcategory"></form>
                <div class="scroll">
                    <table id="tablecategories" class="collapse lefttext">
                        <?php
                            $names = ["Code","Category","Tax"];
                            echo("<tr>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<th title='".$names[$index]."'>".$names[$index]."</th>");
                                }
                            echo("</tr>");
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