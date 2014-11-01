<?php
//need to manually include for the function 'get_declared_classes()'
$dirlib = __DIR__.'/../lib';
include_once("$dirlib/soap/IPPhpDoc.class.php");
include_once("$dirlib/soap/IPReflectionClass.class.php");
include_once("$dirlib/soap/IPReflectionCommentParser.class.php");
include_once("$dirlib/soap/IPReflectionMethod.class.php");
include_once("$dirlib/soap/IPReflectionProperty.class.php");
include_once("$dirlib/soap/IPXMLSchema.class.php");
include_once("$dirlib/soap/WSDLStruct.class.php");
include_once("$dirlib/soap/WSHelper.class.php");
include_once("$dirlib/IPXSLTemplate.class.php");

$phpdoc=new IPPhpdoc();
if(isset($_GET['class'])) $phpdoc->setClass($_GET['class']);
echo $phpdoc->getDocumentation();
