<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/Select.php";
    echo(json_encode(SelectSql("FullSimple","categories")));
    $key = 'I2s2zuzhGSbPsSKjmYN1DOO8T678wI6ZBKpE4oWtWN7vjpqgjZfbLs5h3Wy950o9K6nt';
    $value = 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk=';
    $method = $_SERVER['REQUEST_METHOD'];
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