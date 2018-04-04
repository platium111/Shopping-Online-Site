<?php

$xmlDoc = new DomDocument("1.0");
$xmlDoc->formatOutput = true;
$xmlDoc->preserveWhiteSpace = false;
$folder = "../../data";
$pathToFile = "../../data/goods.xml";

$name = $_GET["name"];
$price = $_GET["price"];
$quantity = $_GET["quantity"];
$description = $_GET["description"];
$itemNumber = 0;

if (!file_exists($pathToFile)) {
    createXML($xmlDoc);
    $xmlDoc->load($pathToFile);
    saveData($itemNumber, $xmlDoc);
    $itemNumber = 1;
} else {
    $xmlDoc->load($pathToFile);
    $root = $xmlDoc->documentElement;
    $goods = $root->getElementsByTagName('good');
    if (count($goods) == 0) {
        $itemNumber = 1; // neu ko co good 
        saveData($itemNumber, $xmlDoc);
    } else if (checkItemDup($xmlDoc) == false) {
        $itemNumber = increaseNum($xmlDoc);
        saveData($itemNumber, $xmlDoc);
    } else
        echo "The item is already exist!";
}

function increaseNum($doc) {
    global $itemNumber;

    $xmlDoc = $doc;
    $root = $xmlDoc->documentElement;
    $arrGood = $root->getElementsByTagName('good');

    foreach ($arrGood as $element) {
        $number = $element->getElementsByTagName('itemNumber')->item(0)->nodeValue;
        $itemNumber = $number + 1;
    }
    return $itemNumber;
}

function createXML($x) {

    global $folder, $pathToFile;
    $xmlDoc = $x;
    $goods = $xmlDoc->createElement('goods');
    $xmlDoc->appendChild($goods);
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
    $xmlDoc->save($pathToFile);
}

function checkItemDup($x) {
    global $name;
    $xmlDoc = $x;
    $root = $xmlDoc->documentElement;
    $arrGood = $root->getElementsByTagName('good');

    foreach ($arrGood as $element) {
        $name_xml = $element->getElementsByTagName('name')->item(0)->nodeValue;
        if ($name == $name_xml) {
            return true;
        }
    }
    return false;
}

function saveData($itemNumber, $doc) {
    global $name, $price, $quantity, $description, $pathToFile;
    $xmlDoc = $doc;
    $xmlDoc->formatOutput = true;
    $number = $itemNumber;
    $root = $xmlDoc->documentElement;
    $goods = $xmlDoc->getElementsByTagName('goods')->item(0);

    $good = $xmlDoc->createElement('good');
    $good = $goods->appendChild($good);

    $newItemNumber = $xmlDoc->createElement('itemNumber');
    $newItemNumber = $good->appendChild($newItemNumber);
    $valueItemNumber = $xmlDoc->createTextNode($number);
    $valueItemNumber = $newItemNumber->appendChild($valueItemNumber);

    $newName = $xmlDoc->createElement('name');
    $newName = $good->appendChild($newName);
    $valueName = $xmlDoc->createTextNode($name);
    $valueName = $newName->appendChild($valueName);

    $newPrice = $xmlDoc->createElement('price');
    $newPrice = $good->appendChild($newPrice);
    $valuePrice = $xmlDoc->createTextNode($price);
    $valuePrice = $newPrice->appendChild($valuePrice);

    $newQuantity = $xmlDoc->createElement('quantity');
    $newQuantity = $good->appendChild($newQuantity);
    $valueQuantity = $xmlDoc->createTextNode($quantity);
    $valueQuantity = $newQuantity->appendChild($valueQuantity);

    $newQuantityHold = $xmlDoc->createElement('quantityHold');
    $newQuantityHold = $good->appendChild($newQuantityHold);
    $valueQuantityHold = $xmlDoc->createTextNode("0");
    $valueQuantityHold = $newQuantityHold->appendChild($valueQuantityHold);

    $newQuantitySold = $xmlDoc->createElement('quantitySold');
    $newQuantitySold = $good->appendChild($newQuantitySold);
    $valueQuantitySold = $xmlDoc->createTextNode("0");
    $valueQuantitySold = $newQuantitySold->appendChild($valueQuantitySold);

    $newDescription = $xmlDoc->createElement('description');
    $newDescription = $good->appendChild($newDescription);
    $valueDescription = $xmlDoc->createTextNode($description);
    $valueDescription = $newDescription->appendChild($valueDescription);


    $xmlDoc->save($pathToFile);
    echo "The item has been listed in the system, and the item number is: '$number'";
}
?>