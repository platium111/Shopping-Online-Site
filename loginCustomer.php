<?php
session_start();
$xmlDoc = new DomDocument("1.0");
$xmlDoc->formatOutput = true;
$xmlDoc->preserveWhiteSpace = false;

$pathToFile = "../../data/customer.xml";

$email = $_GET["email"];
$password = $_GET["password"];
$xmlDoc->load($pathToFile);

if(checkUser($xmlDoc) == 1) {
    $_SESSION["username"] = $email;
    echo "success";
} else {
    echo "Email or password is not match";
}
function checkUser($doc) {
    global $email, $password;
    $xmlDoc = $doc;
    $root = $xmlDoc->documentElement;
    $arrCus = $root->getElementsByTagName('customer');

    foreach ($arrCus as $element) {
        $emailC = $element->getElementsByTagName('email')->item(0)->nodeValue;
        $pw = $element->getElementsByTagName('password')->item(0)->nodeValue;
        if ($email == $emailC && $password == $pw) { 
            return 1;
        }
    }
    return 0;
}
?>