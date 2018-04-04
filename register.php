<?php

$xmlDoc = new DomDocument("1.0");
$xmlDoc->formatOutput = true;
$xmlDoc->preserveWhiteSpace = false;

$folder = "../../data";
$pathToFile = "../../data/customer.xml";
//--INPUT VALUE--
$email = $_GET["email"];
$firstName = $_GET["firstName"];
$lastName = $_GET["lastName"];
$password = $_GET["password"];
$phoneNumber = $_GET["phoneNumber"];
//--
$p1 = "/^\d\d \d\d\d\d\d\d\d\d$/";
$p2 = "/^\(\d\d\)\d\d\d\d\d\d\d\d$/";

if ($phoneNumber == "" || preg_match($p1, $phoneNumber) || preg_match($p2, $phoneNumber)) {
    if (!file_exists($pathToFile)) {
        createXML($xmlDoc);
        $xmlDoc->load($pathToFile);
        saveData($xmlDoc);
    } else {
        $xmlDoc->load($pathToFile);
        $root = $xmlDoc->documentElement;
        $arrCustomer = $root->getElementsByTagName('customer');
        if (count($arrCustomer) == 0)
            saveData($xmlDoc);
        else if (checkEmail($xmlDoc) == 1)
            saveData($xmlDoc);
        else
            echo "Email is already exist";
    }
} else {
    echo "Phone number is not valid. Valid example: 04 12345678 or (04) 12345678";
}

function createXML($doc) {
    global $folder, $pathToFile;
    $xmlDoc = $doc;
    $customers = $xmlDoc->createElement('customers');
    $xmlDoc->appendChild($customers);
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
    $xmlDoc->save($pathToFile);
}

function checkEmail($doc) {
    global $email, $pathToFile;
    $xmlDoc = $doc;
    $root = $xmlDoc->documentElement;
    $arrCustomer = $root->getElementsByTagName('customer');

    foreach ($arrCustomer as $element) {
        $emailXml = $element->getElementsByTagName('email')->item(0)->nodeValue;
        if ($email == $emailXml) {
            return 0;
        }
    }
    return 1;
}

function saveData($doc) {
    global $pathToFile;
    global $email, $firstName, $lastName, $password, $phoneNumber;

    $xmlDoc = $doc;
//    $root = $xmlDoc->documentElement;
    $customers_xml = $xmlDoc->getElementsByTagName('customers')->item(0);

    $customer_xml = $xmlDoc->createElement('customer');
    $customer_xml = $customers_xml->appendChild($customer_xml);

    $email_xml = $xmlDoc->createElement('email');
    $email_xml = $customer_xml->appendChild($email_xml);
    $txtEmail = $xmlDoc->createTextNode($email);
    $txtEmail = $email_xml->appendChild($txtEmail);

    $firstName_xml = $xmlDoc->createElement('firstName');
    $firstName_xml = $customer_xml->appendChild($firstName_xml);
    $txtFirstName = $xmlDoc->createTextNode($firstName);
    $txtFirstName = $firstName_xml->appendChild($txtFirstName);

    $lastName_xml = $xmlDoc->createElement('lastName');
    $lastName_xml = $customer_xml->appendChild($lastName_xml);
    $txtLastName = $xmlDoc->createTextNode($lastName);
    $txtLastName = $lastName_xml->appendChild($txtLastName);

    $password_xml = $xmlDoc->createElement('password');
    $password_xml = $customer_xml->appendChild($password_xml);
    $txtPassword = $xmlDoc->createTextNode($password);
    $txtPassword = $password_xml->appendChild($txtPassword);

    $phoneNumber_xml = $xmlDoc->createElement('phoneNumber');
    $phoneNumber_xml = $customer_xml->appendChild($phoneNumber_xml);
    $txtPhoneNumber = $xmlDoc->createTextNode($phoneNumber);
    $txtPhoneNumber = $phoneNumber_xml->appendChild($txtPhoneNumber);

    $xmlDoc->save($pathToFile);
    echo "Registration successfully";
}
