<?php

ini_set("soap.wsdl_cache_enabled", "0");

class webserviceTests  extends PHPUnit_Framework_TestCase {

    protected $soapClient;

    function setUp() {
        $wsdl = "http://localhost/ws/example/service.php?class=contactManager&wsdl";
        $options = array('actor' =>'http://schema.example.com',
                         'trace' => true,
                         'soap_version' => SOAP_1_1,
                         'classmap'=>array(
                            'address'=>'address',
                            'contact'=>'contact'
                        ),
                         'exceptions'=>1,
                         );
        $this->soapClient = new SoapClient($wsdl, $options);
    }

    function tearDown() {
        $this->soapClient = null;
    }

    function testReturnOfAnArray() {
        $result = $this->soapClient->getContacts();
        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));
        $o = $result[0];
        $this->assertInstanceOf('contact', $o);

        $this->assertEquals('1', $o->id);
        $this->assertEquals('me', $o->name);
        $this->assertInstanceOf('address', $o->address);
        $this->assertEquals('sesamstreet', $o->address->street);
        $this->assertEquals('sesamcity', $o->address->city);
        $this->assertEquals(null, $o->address->zipcode);
        $o = $result[1];
        $this->assertInstanceOf('contact', $o);

        $this->assertEquals('2', $o->id);
        $this->assertEquals('zorg', $o->name);
        $this->assertInstanceOf('address', $o->address);
        $this->assertEquals('toysstreet', $o->address->street);
        $this->assertEquals('toyscity', $o->address->city);
        $this->assertEquals(null, $o->address->zipcode);
    }

    function testReturnAnObject() {
        $o = $this->soapClient->getContact(2);
        $this->assertInstanceOf('contact', $o);

        $this->assertEquals('2', $o->id);
        $this->assertEquals('zorg', $o->name);
        $this->assertInstanceOf('address', $o->address);
        $this->assertEquals('toysstreet', $o->address->street);
        $this->assertEquals('toyscity', $o->address->city);
        $this->assertEquals(null, $o->address->zipcode);
    }

    function testArrayParameter() {
        $list = array();
        $c = new contact();
        $c->name = 'foo';
        $c->address = new address();
        $list[] = $c;
        
        $c = new contact();
        $c->name = 'bar';
        $c->address = new address();
        $c->address->street = 'street';
        $list[] = $c;

        $r = $this->soapClient->addContacts($list);
        $this->assertTrue($r);
    }

    function testAssocArrayParameter() {
        $list = array();
        $c = new contact();
        $c->name = 'foo';
        $c->address = new address();
        $list[$c->name] = $c;
        
        $c = new contact();
        $c->name = 'bar';
        $c->address = new address();
        $c->address->street = 'street';
        $list[$c->name] = $c;

        $r = $this->soapClient->addAssocContacts($list);
        $this->assertTrue($r);
    }

    function testReturnOfAnAssocArray() {
        $result = $this->soapClient->getContactsAsAssoc();

        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));
        $o = $result['me'];
        $this->assertInstanceOf('SoapVar', $o);
        $o = $o->enc_value;
        $this->assertInstanceOf('contact', $o);

        $this->assertEquals('1', $o->id);
        $this->assertEquals('me', $o->name);
        $this->assertInstanceOf('address', $o->address);
        $this->assertEquals('sesamstreet', $o->address->street);
        $this->assertEquals('sesamcity', $o->address->city);
        $this->assertEquals(null, $o->address->zipcode);

        $o = $result['zorg'];
        $this->assertInstanceOf('SoapVar', $o);
        $o = $o->enc_value;
        $this->assertInstanceOf('contact', $o);

        $this->assertEquals('2', $o->id);
        $this->assertEquals('zorg', $o->name);
        $this->assertInstanceOf('address', $o->address);
        $this->assertEquals('toysstreet', $o->address->street);
        $this->assertEquals('toyscity', $o->address->city);
        $this->assertEquals(null, $o->address->zipcode);
    }

    function testReturnOfAnAssocArrayInt() {
        $result = $this->soapClient->getContactIds();
        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));

        $this->assertEquals(1, $result['me']);
        $this->assertEquals(2, $result['zorg']);
    }
}


