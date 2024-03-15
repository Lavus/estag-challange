<?php
  $servername = "pgsql_desafio";
  $username = "root";
  $password = "root";
  $db = "applicationphp";
  try {
    $conn = new PDO("pgsql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage() . "<br><br>");
  }
?>