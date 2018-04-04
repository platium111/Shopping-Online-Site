<?php
session_start();
$username = $_GET["email"];
$password = $_GET["password"];
$fileManager = file("../../data/manager.txt");
$flag = false;
for ($i = 0; $i < count($fileManager); $i++) {
    $all = explode(",", $fileManager[$i]);
//    echo $all[0]."|".strlen($all[1])."|";
    $pass = substr($all[1], 1, strlen($all[1]) - 3); // all pass: 11(length), 1->8
//    echo $pass."|";
    if ($all[0] == $username && $pass == $password) {
        $_SESSION["m_userName"] = $username;
        $_SESSION["m_password"] = $password;
        $flag = true;
        break;
    }
}
if ($flag == true)
    echo "success";
else {
    echo "Login error. Please input correct username and password";
}
?>

