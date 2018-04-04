<?php session_start(); ?>
<?php

$folder = "../../data";
$pathToGood = "../../data/goods.xml";
$pathToCart = "../../data/cart.xml";
$action = $_GET["action"];

if (array_key_exists("itemNumber", $_GET)) {
    $newItemNo = $_GET["itemNumber"];
} else {
    $newItemNo = "0";
}
if (array_key_exists("Cart", $_SESSION)) {
    $cart = $_SESSION["Cart"];
} else {
    $_SESSION["Cart"] = array();
    $cart = $_SESSION["Cart"];
}

$xmlDoc = new DomDocument("1.0");
$xmlDoc->formatOutput = true;
$xmlDoc->preserveWhiteSpace = false;
$xmlDoc->load($pathToGood);
$root = $xmlDoc->documentElement;
$arrGood = $root->getElementsByTagName('good');

$docForCart = new DomDocument("1.0");
$docForCart->formatOutput = true;
$docForCart->preserveWhiteSpace = false;
if (!file_exists($pathToCart)) {
    createXML($docForCart);
    $docForCart->load($pathToCart);
} else {
    $docForCart->load($pathToCart);
}


if ($action == "confirm") {
    performPurchase("confirm");
} else if ($action == "add") {
//    echo "go here";
    foreach ($arrGood as $good) {
        $itemNoXml = $good->getElementsByTagName('itemNumber')->item(0)->nodeValue;
        $quantityXml = $good->getElementsByTagName('quantity')->item(0)->nodeValue;
        $quantityHoldXml = $good->getElementsByTagName('quantityHold')->item(0)->nodeValue;
        if ($itemNoXml == $newItemNo) {// (1) if equals from select in form
            if ($quantityXml > 0) { // (2) if quan > 0
                //(1)--START: HANDING CART session
                if (count($cart) == 0) { // nothing in cart
                    $cart[$newItemNo] = 1;
                } else { // have exist 
                    if ($cart[$newItemNo] > 0) { // exist key = newItemNo
                        $oldQuantity = $cart[$newItemNo];
                        $cart[$newItemNo] = $oldQuantity + 1;
                    } else {
                        $cart[$newItemNo] = 1;
                    }
                }
                $_SESSION["Cart"] = $cart; // (4)update cart
                //--END: HANDING CART session
                //(2)--START HANDING GOODS quantity, quantity hold
                $good->getElementsByTagName('quantity')->item(0)->nodeValue = $quantityXml - 1; // -1 quantity
                $good->getElementsByTagName('quantityHold')->item(0)->nodeValue = $quantityHoldXml + 1; // +1 Hold
                $xmlDoc->save($pathToGood);
                //--END HANDING GOODS
                //(3)-- HANDING CART XML
                saveCart($cart, "add");
                //--END
                //(4) display
                transformXsl("showCart.xsl");
            } else {
                //TODO : when click add and decrease 0
                saveCart($cart, "add");
//            // display
                transformXsl("showCart.xsl");
            }
        }
    }
} else if ($action == "remove") {
    // Remove in cart session
    global $cart;
    $oldQuantity = $cart[$newItemNo];
    $cart[$newItemNo] = $oldQuantity - 1;
    $_SESSION["Cart"] = $cart;
    //edit goods.xml +1 quantity -1 hold
    foreach ($arrGood as $good) {
        $itemNoXml = $good->getElementsByTagName('itemNumber')->item(0)->nodeValue;
        $quantityXml = $good->getElementsByTagName('quantity')->item(0)->nodeValue;
        $quantityHoldXml = $good->getElementsByTagName('quantityHold')->item(0)->nodeValue;
        if ($itemNoXml == $newItemNo) {
            $good->getElementsByTagName('quantity')->item(0)->nodeValue = $quantityXml + 1; // -1 quantity
            $good->getElementsByTagName('quantityHold')->item(0)->nodeValue = $quantityHoldXml - 1; // +1 Hold
            $xmlDoc->save($pathToGood);
        }
    }
    // edit cart.xml -1 quantity
    saveCart($cart, "remove");
    // display transform
    transformXsl("showCart.xsl");
} else if ($action == "cancel") {
    performPurchase("cancel");
} else if($action == "logout") {
    performPurchase("cancel");
    echo "Thanks for visiting out website. Your customer id is: ".$_SESSION["username"];
} else if($action =="m_logout") {
    echo "Your manager id is: ".$_SESSION["m_userName"];
}

function performPurchase($action) {
    global $cart, $arrGood, $docForCart, $xmlDoc, $pathToCart, $pathToGood;
    // UPDATE goods.xml
    foreach ($arrGood as $good) {
        $itemNoXml = $good->getElementsByTagName('itemNumber')->item(0)->nodeValue;
        $quantityHoldXml = $good->getElementsByTagName('quantityHold')->item(0)->nodeValue;
        $quantitySoldXml = $good->getElementsByTagName('quantitySold')->item(0)->nodeValue;
        $quantityXml = $good->getElementsByTagName('quantity')->item(0)->nodeValue;
//        $priceXml = $good->getElementsByTagName('price')->item(0)->nodeValue;

        foreach ($cart as $key => $value) {
            if ($key == $itemNoXml) {
                //confirm : - quanHold | + quan sold in cart session
                //cancel : -hold | + quan avai
                $good->getElementsByTagName('quantityHold')->item(0)->nodeValue = $quantityHoldXml - $value;
                if ($action == "confirm") {
                    $good->getElementsByTagName('quantitySold')->item(0)->nodeValue = $quantitySoldXml + $value;
                }
                if ($action == "cancel") {
                    
                    $good->getElementsByTagName('quantity')->item(0)->nodeValue = $quantityXml + $value;
                }
            }
        }
    }

    //delete xml from cart -> save
//    $goodXmlList = $docForCart->getElementsByTagName('good');
    $cartXml = $docForCart->getElementsByTagName('cart')->item(0);
    while ($cartXml->hasChildNodes()) {
        $cartXml->removeChild($cartXml->firstChild);
    }
    //update xml files
    $docForCart->save($pathToCart);
    $xmlDoc->save($pathToGood);
    transformXsl("showCart.xsl");
    // UPDATE CART
    $cart = array();
    $_SESSION["Cart"] = $cart;
}

//displayDump($cart);
// $cartArr ~ cart session (itemno-quantity) | 
// $arrGood ~ all good from goods.xml (price)
function saveCart($cartArr, $action) {
    global $xmlDoc, $pathToCart, $docForCart, $newItemNo, $cart;
    $root = $xmlDoc->documentElement;
    $arrGood = $root->getElementsByTagName('good');
    $cartXml = $docForCart->getElementsByTagName('cart')->item(0);
    //(1)--START: GET price from good based on itemNo -> calculate total
    $priceFrGood = 0;
    $total = 0;
    foreach ($cartArr as $itemNo => $quantity) {
        foreach ($arrGood as $element) {
            $itemNumberGood = $element->getElementsByTagName('itemNumber')->item(0)->nodeValue;
            if ($itemNo == $itemNumberGood) {
                $priceFrGood = $element->getElementsByTagName('price')->item(0)->nodeValue;
                $total += ($priceFrGood * $quantity);
            }
        }
    }
    //--END GET PRICE
    //(2)--START WRITE GOOD IN cart.xml
    $arrGoodInCart = $cartXml->getElementsByTagName('good');
    $flagEqual = false;
    if ($arrGoodInCart->length > 0) { // if exist good
        $quantityFoundInCart = 0;
        foreach ($arrGoodInCart as $good) {
            $itemNumberFrCart = $good->getElementsByTagName('itemNumber')->item(0)->nodeValue;
            $quantityFrCart = $good->getElementsByTagName('quantity')->item(0)->nodeValue;

            if ($itemNumberFrCart == $newItemNo) { // check equal to newItemNo if !equal -> create new good else increase 
                if ($action == "add") {
                    $flagEqual = true;
                    $good->getElementsByTagName('quantity')->item(0)->nodeValue = $quantityFrCart + 1;
                } else {
                    if ($quantityFrCart == 1) {
//                        echo "1";
                        $cartXml->removeChild($good);
                    } else {
//                        echo "2";
                        $good->getElementsByTagName('quantity')->item(0)->nodeValue = $quantityFrCart - 1;
                    }
                }
                $flagEqual = true;
            }
        }
        if ($flagEqual == FALSE) { // them cai moi ngoai item trong cart
//            echo "3";
            createGood($docForCart, $cartXml, $newItemNo, $priceFrGood, $cart[$newItemNo]);
            $flagEqual = false;
        }
    } else { // not exist any good
//        echo "4";
        createGood($docForCart, $cartXml, $newItemNo, $priceFrGood, $cart[$newItemNo]);
        $flagEqual = false;
    }
    //--END WRITE GOOD
    //(3)--START WRITE TOTAL
    if ($cartXml->getElementsByTagName("total")->length > 0) {
        $cartXml->getElementsByTagName("total")->item(0)->nodeValue = $total;
    } else {
        $totalXml = $cartXml->appendChild($docForCart->createElement('total'));
        $totalXml->appendChild($docForCart->createTextNode($total));
    }
    $docForCart->save($pathToCart);
    //--END
}

function createGood($docForCart, $cart, $itemNumber, $priceFrGood, $quantity) {
    $goodXml = $cart->appendChild($docForCart->createElement('good'));

    $itemNumberXml = $goodXml->appendChild($docForCart->createElement('itemNumber'));
    $itemNumberXml->appendChild($docForCart->createTextNode($itemNumber));

    $priceXml = $goodXml->appendChild($docForCart->createElement('price'));
    $priceXml->appendChild($docForCart->createTextNode($priceFrGood));

    $quantityXml = $goodXml->appendChild($docForCart->createElement('quantity'));
    $quantityXml->appendChild($docForCart->createTextNode($quantity));
}

function transformXsl($name) {
    global $pathToCart;
    $xmlDoc = new DomDocument("1.0");
    $xmlDoc->formatOutput = true;
    $xmlDoc->preserveWhiteSpace = false;
    $xmlDoc->load($pathToCart);

    $xslDoc = new DomDocument("1.0");
    $xslDoc->load($name);

    $proc = new XSLTProcessor;
    $proc->importStyleSheet($xslDoc);
    echo $proc->transformToXML($xmlDoc);
}

function displayDump($arr) {
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

function createXML($docForCart) {
    global $folder, $pathToCart;
    $cart = $docForCart->createElement('cart');
    $docForCart->appendChild($cart);

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
    $docForCart->save($pathToCart);
}

?>