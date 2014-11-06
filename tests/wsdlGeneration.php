<?php

class testWsdlSimpleClass {

	/**
	 * @param integer foo
	 * @return void
	 */
	public function meth1($num) {}
}

class testWsdlWithArray {

	/**
	 * @param string[] foo
	 * @return void
	 */
	public function meth1($arr1) {}

	/**
	 * @param array(string) something2
	 * @return void
	 */
	public function meth2($arr2) {}

	/**
	 * @param testWsdlSimpleClass[] something3
	 * @return void
	 */
	public function meth3($arr3) {}

	/**
	 * @param array(testWsdlSimpleClass[]) something4
	 * @return void
	 */
	public function meth4($arr4, array $arr42) {}

	/**
	 * @param array() something5.1
	 * @param array something5.2
	 * @param array(array()) something5.3
	 * @return void
	 */
	public function meth5($arr51, $arr52, $arr53) {}

}

class testWsdlWithMixed {

	/**
	 * it shouldn't appear in the wsdl
	 */
	public function __construct() {}

	/**
	 * it shouldn't appear in the wsdl
	 */
	public function __get($name) {}

	/**
	 * @return void
	 */
	public function meth1($mx1) {}

	/**
	 * @param mixed foo
	 * @return void
	 */
	public function meth2($mx2) {}

	/**
	 * @param mixed[] foo
	 * @return void
	 */
	public function meth3($mx3) {}
}



class testWsdlWithObject {

	/**
	 * @return void
	 */
	function meth1(testWsdlSimpleClass $arg1) {}

	/**
	 * @param object something
	 * @return object
	 */
	function meth2($arg2) {}

	/**
	 * @param object(foo=>integer,bar=>testWsdlSimpleClass)  a stdclass object
	 * @return void
	 */
	function meth3($arg3) {}

	/**
	 * @param testWsdlSimpleClass lorem1 ipsum1
	 * @param string lorem2 ipsum2
	 * @param integer a number
	 * @param string a string
	 * @return void
	 */
	function meth4(testWsdlSimpleClass $paramObj, $arr, $num, $str) {}

}



class testWsdlWithAssocArray {

	/**
     * @param string[=>]
	 * @return void
	 */
	function meth1($arr1) {}

	/**
	 * @param testWsdlSimpleClass[=>] something
	 * @return integer[=>]
	 */
	function meth2($arr2) {}

	/**
	 * @param array(testWsdlSimpleClass[=>])
	 * @return void
	 */
	function meth3($arr3) {}
}

class wsdlGenerationTests  extends PHPUnit_Framework_TestCase {

	function testSimpleClass() {
		$class = new IPReflectionClass('testWsdlSimpleClass');
		$wsdl = new WSDLStruct('http://localhost/my/namespace', 'http://localhost/wsdl/uri' );
		$wsdl->setService($class);
		$gendoc = $wsdl->generateDocument();
		$gendoc = str_replace("><", ">\n<", $gendoc);
		$expected = '<'.'?xml version="1.0"?'.'>'. "\n";
		$expected .='<wsdl:definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://localhost/my/namespace" targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=	'<wsdl:types>'."\n";
		$expected .=		'<xsd:schema targetNamespace="http://localhost/my/namespace"/>'."\n";
		$expected .=	'</wsdl:types>'."\n";
		$expected .=	'<message name="meth1Request">'."\n";
		$expected .=		'<part name="num" type="xsd:int"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<wsdl:portType name="testWsdlSimpleClassPortType">'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<wsdl:input message="tns:meth1Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</wsdl:portType>'."\n";
		$expected .=	'<binding name="testWsdlSimpleClassBinding" type="tns:testWsdlSimpleClassPortType">'."\n";
		$expected .=		'<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth1" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</binding>'."\n";
		$expected .=	'<wsdl:service name="testWsdlSimpleClass">'."\n";
		$expected .=		'<wsdl:port name="testWsdlSimpleClassPort" binding="tns:testWsdlSimpleClassBinding">'."\n";
		$expected .=			'<soap:address location="http://localhost/wsdl/uri"/>'."\n";
		$expected .=		'</wsdl:port>'."\n";
		$expected .=	'</wsdl:service>'."\n";
		$expected .='</wsdl:definitions>'."\n";

		$this->assertEquals($expected, $gendoc);
	}

	function testArray() {
		$class = new IPReflectionClass('testWsdlWithArray');
		$wsdl = new WSDLStruct('http://localhost/my/namespace', 'http://localhost/wsdl/uri' );
		$wsdl->setService($class);
		$gendoc = $wsdl->generateDocument();
		$gendoc = str_replace("><", ">\n<", $gendoc);
		$expected = '<'.'?xml version="1.0"?'.'>'. "\n";
		$expected .='<wsdl:definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://localhost/my/namespace" targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=	'<wsdl:types>'."\n";
		$expected .=		'<xsd:schema targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=			'<xsd:complexType name="ArrayOfstring">'."\n";
		$expected .=				'<xsd:complexContent>'."\n";
		$expected .=					'<xsd:restriction base="SOAP-ENC:Array">'."\n";
		$expected .=						'<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="xsd:string[]"/>'."\n";
		$expected .=					'</xsd:restriction>'."\n";
		$expected .=				'</xsd:complexContent>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="testWsdlSimpleClass">'."\n";
		$expected .=				'<xsd:all/>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="ArrayOftestWsdlSimpleClass">'."\n";
		$expected .=				'<xsd:complexContent>'."\n";
		$expected .=					'<xsd:restriction base="SOAP-ENC:Array">'."\n";
		$expected .=						'<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="tns:testWsdlSimpleClass[]"/>'."\n";
		$expected .=					'</xsd:restriction>'."\n";
		$expected .=				'</xsd:complexContent>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="ArrayOfArrayOftestWsdlSimpleClass">'."\n";
		$expected .=				'<xsd:complexContent>'."\n";
		$expected .=					'<xsd:restriction base="SOAP-ENC:Array">'."\n";
		$expected .=						'<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="tns:ArrayOftestWsdlSimpleClass[]"/>'."\n";
		$expected .=					'</xsd:restriction>'."\n";
		$expected .=				'</xsd:complexContent>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="ArrayOfanyType">'."\n";
		$expected .=				'<xsd:complexContent>'."\n";
		$expected .=					'<xsd:restriction base="SOAP-ENC:Array">'."\n";
		$expected .=						'<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="xsd:anyType[]"/>'."\n";
		$expected .=					'</xsd:restriction>'."\n";
		$expected .=				'</xsd:complexContent>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="ArrayOfArrayOfanyType">'."\n";
		$expected .=				'<xsd:complexContent>'."\n";
		$expected .=					'<xsd:restriction base="SOAP-ENC:Array">'."\n";
		$expected .=						'<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="tns:ArrayOfanyType[]"/>'."\n";
		$expected .=					'</xsd:restriction>'."\n";
		$expected .=				'</xsd:complexContent>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=		'</xsd:schema>'."\n";
		$expected .=	'</wsdl:types>'."\n";
		$expected .=	'<message name="meth1Request">'."\n";
		$expected .=		'<part name="arr1" type="tns:ArrayOfstring"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth2Request">'."\n";
		$expected .=		'<part name="arr2" type="tns:ArrayOfstring"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth3Request">'."\n";
		$expected .=		'<part name="arr3" type="tns:ArrayOftestWsdlSimpleClass"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth4Request">'."\n";
		$expected .=		'<part name="arr4" type="tns:ArrayOfArrayOftestWsdlSimpleClass"/>'."\n";
		$expected .=		'<part name="arr42" type="tns:ArrayOfanyType"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth5Request">'."\n";
		$expected .=		'<part name="arr51" type="tns:ArrayOfanyType"/>'."\n";
		$expected .=		'<part name="arr52" type="tns:ArrayOfanyType"/>'."\n";
		$expected .=		'<part name="arr53" type="tns:ArrayOfArrayOfanyType"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<wsdl:portType name="testWsdlWithArrayPortType">'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<wsdl:input message="tns:meth1Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<wsdl:input message="tns:meth2Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<wsdl:input message="tns:meth3Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth4">'."\n";
		$expected .=			'<wsdl:input message="tns:meth4Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth5">'."\n";
		$expected .=			'<wsdl:input message="tns:meth5Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</wsdl:portType>'."\n";
		$expected .=	'<binding name="testWsdlWithArrayBinding" type="tns:testWsdlWithArrayPortType">'."\n";
		$expected .=		'<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth1" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth2" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth3" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth4">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth4" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth5">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth5" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</binding>'."\n";
		$expected .=	'<wsdl:service name="testWsdlWithArray">'."\n";
		$expected .=		'<wsdl:port name="testWsdlWithArrayPort" binding="tns:testWsdlWithArrayBinding">'."\n";
		$expected .=			'<soap:address location="http://localhost/wsdl/uri"/>'."\n";
		$expected .=		'</wsdl:port>'."\n";
		$expected .=	'</wsdl:service>'."\n";
		$expected .='</wsdl:definitions>'."\n";
		$this->assertEquals($expected, $gendoc);
	}

	function testWsdlWithMixed() {
		$class = new IPReflectionClass('testWsdlWithMixed');
		$wsdl = new WSDLStruct('http://localhost/my/namespace', 'http://localhost/wsdl/uri' );
		$wsdl->setService($class);
		$gendoc = $wsdl->generateDocument();
		$gendoc = str_replace("><", ">\n<", $gendoc);
		$expected = '<'.'?xml version="1.0"?'.'>'. "\n";
		$expected .='<wsdl:definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://localhost/my/namespace" targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=	'<wsdl:types>'."\n";
		$expected .=		'<xsd:schema targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=			'<xsd:complexType name="ArrayOfanyType">'."\n";
		$expected .=				'<xsd:complexContent>'."\n";
		$expected .=					'<xsd:restriction base="SOAP-ENC:Array">'."\n";
		$expected .=						'<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="xsd:anyType[]"/>'."\n";
		$expected .=					'</xsd:restriction>'."\n";
		$expected .=				'</xsd:complexContent>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=		'</xsd:schema>'."\n";
		$expected .=	'</wsdl:types>'."\n";
		$expected .=	'<message name="meth1Request">'."\n";
		$expected .=		'<part name="mx1" type="xsd:anyType"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth2Request">'."\n";
		$expected .=		'<part name="mx2" type="xsd:anyType"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth3Request">'."\n";
		$expected .=		'<part name="mx3" type="tns:ArrayOfanyType"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<wsdl:portType name="testWsdlWithMixedPortType">'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<wsdl:input message="tns:meth1Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<wsdl:input message="tns:meth2Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<wsdl:input message="tns:meth3Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</wsdl:portType>'."\n";
		$expected .=	'<binding name="testWsdlWithMixedBinding" type="tns:testWsdlWithMixedPortType">'."\n";
		$expected .=		'<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth1" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth2" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth3" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</binding>'."\n";
		$expected .=	'<wsdl:service name="testWsdlWithMixed">'."\n";
		$expected .=		'<wsdl:port name="testWsdlWithMixedPort" binding="tns:testWsdlWithMixedBinding">'."\n";
		$expected .=			'<soap:address location="http://localhost/wsdl/uri"/>'."\n";
		$expected .=		'</wsdl:port>'."\n";
		$expected .=	'</wsdl:service>'."\n";
		$expected .='</wsdl:definitions>'."\n";
		$this->assertEquals($expected, $gendoc);
	}


	function testObjects() {
		$class = new IPReflectionClass('testWsdlWithObject');
		$wsdl = new WSDLStruct('http://localhost/my/namespace', 'http://localhost/wsdl/uri' );
		$wsdl->setService($class);
		$gendoc = $wsdl->generateDocument();
		$gendoc = str_replace("><", ">\n<", $gendoc);
		$expected = '<'.'?xml version="1.0"?'.'>'. "\n";
		$expected .='<wsdl:definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://localhost/my/namespace" targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=	'<wsdl:types>'."\n";
		$expected .=		'<xsd:schema targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=			'<xsd:complexType name="testWsdlSimpleClass">'."\n";
		$expected .=				'<xsd:all/>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="stdClass">'."\n";
		$expected .=				'<xsd:all/>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="object(foo=&gt;integer,bar=&gt;testWsdlSimpleClass)">'."\n";
		$expected .=				'<xsd:all>'."\n";
		$expected .=					'<xsd:element name="foo" type="xsd:int"/>'."\n";
		$expected .=					'<xsd:element name="bar" type="tns:testWsdlSimpleClass"/>'."\n";
		$expected .=				'</xsd:all>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=		'</xsd:schema>'."\n";
		$expected .=	'</wsdl:types>'."\n";
		$expected .=	'<message name="meth1Request">'."\n";
		$expected .=		'<part name="arg1" type="tns:testWsdlSimpleClass"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth2Request">'."\n";
		$expected .=		'<part name="arg2" type="tns:stdClass"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth2Response">'."\n";
		$expected .=		'<part name="meth2Return" type="tns:stdClass"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth3Request">'."\n";
		$expected .=		'<part name="arg3" type="tns:object(foo=&gt;integer,bar=&gt;testWsdlSimpleClass)"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth4Request">'."\n";
		$expected .=		'<part name="paramObj" type="tns:testWsdlSimpleClass"/>'."\n";
		$expected .=		'<part name="arr" type="xsd:string"/>'."\n";
		$expected .=		'<part name="num" type="xsd:int"/>'."\n";
		$expected .=		'<part name="str" type="xsd:string"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<wsdl:portType name="testWsdlWithObjectPortType">'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<wsdl:input message="tns:meth1Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<wsdl:input message="tns:meth2Request"/>'."\n";
		$expected .=			'<wsdl:output message="tns:meth2Response"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<wsdl:input message="tns:meth3Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth4">'."\n";
		$expected .=			'<wsdl:input message="tns:meth4Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</wsdl:portType>'."\n";
		$expected .=	'<binding name="testWsdlWithObjectBinding" type="tns:testWsdlWithObjectPortType">'."\n";
		$expected .=		'<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth1" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth2" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=			'<wsdl:output>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:output>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth3" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth4">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth4" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</binding>'."\n";
		$expected .=	'<wsdl:service name="testWsdlWithObject">'."\n";
		$expected .=		'<wsdl:port name="testWsdlWithObjectPort" binding="tns:testWsdlWithObjectBinding">'."\n";
		$expected .=			'<soap:address location="http://localhost/wsdl/uri"/>'."\n";
		$expected .=		'</wsdl:port>'."\n";
		$expected .=	'</wsdl:service>'."\n";
		$expected .='</wsdl:definitions>'."\n";
		$this->assertEquals($expected, $gendoc);
	}

	function testAssociativeArray() {
		$class = new IPReflectionClass('testWsdlWithAssocArray');
		$wsdl = new WSDLStruct('http://localhost/my/namespace', 'http://localhost/wsdl/uri' );
		$wsdl->setService($class);
		$gendoc = $wsdl->generateDocument();
		$gendoc = str_replace("><", ">\n<", $gendoc);
		$expected = '<'.'?xml version="1.0"?'.'>'. "\n";
		$expected .='<wsdl:definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://localhost/my/namespace" targetNamespace="http://localhost/my/namespace">'."\n";
		$expected .=	'<wsdl:types>'."\n";
		$expected .=		'<xsd:schema targetNamespace="http://localhost/my/namespace">'."\n";

		$expected .=			'<xsd:complexType name="AssociativeArrayOfstring">'."\n";
		$expected .=				'<xsd:sequence>'."\n";
		$expected .=					'<xsd:element name="item" minOccurs="0" maxOccurs="unbounded" type="tns:AssociativeArrayOfstring_item"/>'."\n";
		$expected .=				'</xsd:sequence>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="AssociativeArrayOfstring_item">'."\n";
		$expected .=				'<xsd:sequence>'."\n";
		$expected .=					'<xsd:element name="key" type="xsd:anyType"/>'."\n";
		$expected .=					'<xsd:element name="value" type="xsd:string"/>'."\n";
		$expected .=				'</xsd:sequence>'."\n";
		$expected .=			'</xsd:complexType>'."\n";

		$expected .=			'<xsd:complexType name="testWsdlSimpleClass">'."\n";
		$expected .=				'<xsd:all/>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="AssociativeArrayOftestWsdlSimpleClass">'."\n";
		$expected .=				'<xsd:sequence>'."\n";
		$expected .=					'<xsd:element name="item" minOccurs="0" maxOccurs="unbounded" type="tns:AssociativeArrayOftestWsdlSimpleClass_item"/>'."\n";
		$expected .=				'</xsd:sequence>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="AssociativeArrayOftestWsdlSimpleClass_item">'."\n";
		$expected .=				'<xsd:sequence>'."\n";
		$expected .=					'<xsd:element name="key" type="xsd:anyType"/>'."\n";
		$expected .=					'<xsd:element name="value" type="tns:testWsdlSimpleClass"/>'."\n";
		$expected .=				'</xsd:sequence>'."\n";
		$expected .=			'</xsd:complexType>'."\n";

		$expected .=			'<xsd:complexType name="AssociativeArrayOfint">'."\n";
		$expected .=				'<xsd:sequence>'."\n";
		$expected .=					'<xsd:element name="item" minOccurs="0" maxOccurs="unbounded" type="tns:AssociativeArrayOfint_item"/>'."\n";
		$expected .=				'</xsd:sequence>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=			'<xsd:complexType name="AssociativeArrayOfint_item">'."\n";
		$expected .=				'<xsd:sequence>'."\n";
		$expected .=					'<xsd:element name="key" type="xsd:anyType"/>'."\n";
		$expected .=					'<xsd:element name="value" type="xsd:int"/>'."\n";
		$expected .=				'</xsd:sequence>'."\n";
		$expected .=			'</xsd:complexType>'."\n";

		$expected .=			'<xsd:complexType name="ArrayOfAssociativeArrayOftestWsdlSimpleClass">'."\n";
		$expected .=				'<xsd:complexContent>'."\n";
		$expected .=					'<xsd:restriction base="SOAP-ENC:Array">'."\n";
		$expected .=						'<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="tns:AssociativeArrayOftestWsdlSimpleClass[]"/>'."\n";
		$expected .=					'</xsd:restriction>'."\n";
		$expected .=				'</xsd:complexContent>'."\n";
		$expected .=			'</xsd:complexType>'."\n";
		$expected .=		'</xsd:schema>'."\n";
		$expected .=	'</wsdl:types>'."\n";
		$expected .=	'<message name="meth1Request">'."\n";
		$expected .=		'<part name="arr1" type="tns:AssociativeArrayOfstring"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth2Request">'."\n";
		$expected .=		'<part name="arr2" type="tns:AssociativeArrayOftestWsdlSimpleClass"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth2Response">'."\n";
		$expected .=		'<part name="meth2Return" type="tns:AssociativeArrayOfint"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<message name="meth3Request">'."\n";
		$expected .=		'<part name="arr3" type="tns:ArrayOfAssociativeArrayOftestWsdlSimpleClass"/>'."\n";
		$expected .=	'</message>'."\n";
		$expected .=	'<wsdl:portType name="testWsdlWithAssocArrayPortType">'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<wsdl:input message="tns:meth1Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<wsdl:input message="tns:meth2Request"/>'."\n";
		$expected .=			'<wsdl:output message="tns:meth2Response"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<wsdl:input message="tns:meth3Request"/>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</wsdl:portType>'."\n";
		$expected .=	'<binding name="testWsdlWithAssocArrayBinding" type="tns:testWsdlWithAssocArrayPortType">'."\n";
		$expected .=		'<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>'."\n";
		$expected .=		'<wsdl:operation name="meth1">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth1" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth2">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth2" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=			'<wsdl:output>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:output>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=		'<wsdl:operation name="meth3">'."\n";
		$expected .=			'<soap:operation soapAction="http://localhost/wsdl/uri&amp;method=meth3" style="rpc"/>'."\n";
		$expected .=			'<wsdl:input>'."\n";
		$expected .=				'<soap:body use="encoded" namespace="http://localhost/my/namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>'."\n";
		$expected .=			'</wsdl:input>'."\n";
		$expected .=		'</wsdl:operation>'."\n";
		$expected .=	'</binding>'."\n";
		$expected .=	'<wsdl:service name="testWsdlWithAssocArray">'."\n";
		$expected .=		'<wsdl:port name="testWsdlWithAssocArrayPort" binding="tns:testWsdlWithAssocArrayBinding">'."\n";
		$expected .=			'<soap:address location="http://localhost/wsdl/uri"/>'."\n";
		$expected .=		'</wsdl:port>'."\n";
		$expected .=	'</wsdl:service>'."\n";
		$expected .='</wsdl:definitions>'."\n";

		$this->assertEquals($expected, $gendoc);
	}

}
