<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/InsertSql.php";
    require_once __DIR__."/../security/CheckValidityCode.php";
    require_once __DIR__."/../security/CheckNameAvaliable.php";
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_DGKQPLCQX1IZAO0D7VD9SJYROFSBGSEOQXXHYIXQCQFT2XODQPE8FDRHHJDWY3L5WNBAU6JLA7U44HPXKJDOJ2JBQZCCEK7Y37CC0PILUUMHVTVDYZI5W'])) {
            if ($_SERVER['HTTP_DGKQPLCQX1IZAO0D7VD9SJYROFSBGSEOQXXHYIXQCQFT2XODQPE8FDRHHJDWY3L5WNBAU6JLA7U44HPXKJDOJ2JBQZCCEK7Y37CC0PILUUMHVTVDYZI5W'] == 'eh63/uT/+iQqmpgn3lQWB8ehzIk6Pol+dBqmQhBubW+S1KkNsossNfJIqE+VIHR0w9qHsvgsuthTYR1LW0MsyhGoXUXCWhhE404j9B6yISSHgBRGWBpaY+vGgeEQaRkNiaZwRgJKFc94AX6CBAblSssXviFuO2k='){
                $json = trim(file_get_contents('php://input'));
                $data = json_decode($json,true);
                if ( ( !empty($data['type']) ) && ( !empty($data['code']) ) && ( !empty($data['table']) ) ) {
                    if ( (strval($data['code']) !== '0') && (intval($data['code']) === 0) ){
                        echo(json_encode(false));
                    } else if ( CheckValidityCode($data['code'],$data['table']) ){
                        if ($data['type'] == "Simple") {
                            echo( json_encode ( DeleteSql( $data['type'], $data['table'], $data['code'] ) ) );
                        } else {
                            echo(json_encode(false));
                        }
                    } else {
                        echo(json_encode(false));
                    }
                } else {
                    echo(json_encode(false));
                }
            } else {
                echo(json_encode(false));
            }
        } else {
            echo(json_encode(false));
        }
    } else {
        echo(json_encode(false));
    }
?>