<?php
require_once __DIR__."/common.php";

if(isset($_GET['class']) && (in_array($_GET['class'], $WSClasses) || in_array($_GET['class'], $WSStructures))) {
	$WSHelper = new WSHelper("http://schema.example.com", $_GET['class']);
	$WSHelper->actor = "http://schema.example.com";
	$WSHelper->use = SOAP_ENCODED; 
	$WSHelper->classNameArr = $WSClasses;
	$WSHelper->structureMap = $WSStructures;
	$WSHelper->setPersistence(SOAP_PERSISTENCE_REQUEST);
	$WSHelper->setWSDLCacheFolder(__DIR__.'/wsdl/'); //trailing slash mandatory. Default is 'wsdl/'
	$WSHelper->docTemplate =  __DIR__."/../doc/templates/docclass.xsl";
	try {
		$WSHelper->handle();
		//possible db transaction commit
	}catch(Exception $e) {
		//possible db transaction rollback
		$WSHelper->fault("SERVER", $e->getMessage(),"", $e->__toString());
	}
} else {
	die("No valid class selected");
}
?>