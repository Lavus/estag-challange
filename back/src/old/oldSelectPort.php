<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/Select.php";

    $key = 'I2s2zuzhGSbPsSKjmYN1DOO8T678wI6ZBKpE4oWtWN7vjpqgjZfbLs5h3Wy950o9K6nt';
    $value = 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk=';
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT'])) {
            if ($_SERVER['HTTP_I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT'] == 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='){
                $json = file_get_contents('php://input');
                $data = json_decode($json,true);
                error_log($data['code']);
                if ( (!empty($data['type'])) && (!empty($data['table'])) && (isset($data['code'])) ) {
                    echo(json_encode(SelectSql(strval($data['type']),strval($data['table']),strval($data['code']))));
                } else {
                    echo(json_encode(array()));
                }
            } else {
                echo(json_encode(array()));
            }
        } else {
            echo(json_encode(array()));
        }
    } else {
        echo(json_encode(array()));
    }

    
    // var_dump($_SERVER);
    // error_log(strval('serverlogSTART'));
    // foreach($_SERVER as $keylog => $keylogvalue ) {
    //     if (is_array($keylogvalue)){
    //         error_log(strval('key : '.$keylog.', ARRAYstart'));
    //         foreach($keylogvalue as $log => $logvalue) {
    //             error_log(strval('key : '.$log.', value : '.$logvalue));
    //         }
    //         error_log(strval('key : '.$keylog.', ARRAYend'));
    //     } else {
    //         error_log(strval('key : '.$keylog.', value : '.$keylogvalue));
    //     }
    // }
    // error_log(strval('serverlogEND'));

        //     // ;
        //     // error_log(array($json));
        //     if ($json[0] != '%'){
        //         echo(json_encode($json));
        //     } else {
        //         echo(json_encode(SelectSql("FullSimple","categories")));
        //     }
        //     // echo(json_encode(SelectSql("FullSimple","categories")));
        // }        


    // $json = file_get_contents('php://input');
    // $data = json_decode($json,true);
    // error_log(strval($data['type']));
    // echo(json_encode(SelectSql("FullSimple","categories")));

    // } else {

    // }

    // $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
    // echo(json_encode($_SERVER));

// switch ($method) {
//     case 'GET':
//       $id = $_GET['id'];
//       $sql = "select * from contacts".($id?" where id=$id":''); 
//       break;
//     case 'POST':
//       $name = $_POST["name"];
//       $email = $_POST["email"];
//       $country = $_POST["country"];
//       $city = $_POST["city"];
//       $job = $_POST["job"];

//       $sql = "insert into contacts (name, email, city, country, job) values ('$name', '$email', '$city', '$country', '$job')"; 
//       break;
// }

// if ($method == 'GET') {
//     if (!$id) echo '[';
//     for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
//       echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
//     }
//     if (!$id) echo ']';
//   } elseif ($method == 'POST') {
//     echo json_encode($result);
//   } else {
//     echo mysqli_affected_rows($con);
//   }
?>