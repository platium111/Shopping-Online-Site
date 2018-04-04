<?php

$pathToFile = "../../data/goods.xml";
$xmlDoc = new DomDocument("1.0");
$xmlDoc->formatOutput = true;
$xmlDoc->preserveWhiteSpace = false;
$xmlDoc->load($pathToFile);

$xslDoc = new DomDocument("1.0");
$xslDoc->load("showCatalog.xsl");

$proc = new XSLTProcessor;
$proc->importStyleSheet($xslDoc);
echo $proc->transformToXML($xmlDoc);
?>