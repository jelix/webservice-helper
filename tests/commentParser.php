<?php

class testCommentClass {
	public $return = "";
	public $parameters = array();
	public $fullDescription = "";
	public $smallDescription = "";
	public $throws="";
}


class commentParserTests  extends PHPUnit_Framework_TestCase {

	function testEmptyDescription () {
		$comment = "	/**
		* 
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('', $obj->smallDescription);
		$this->assertEquals('', $obj->fullDescription);

	}

	function testSmallDescription () {
		$comment = "	/**
		* hello world
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals(' hello world', $obj->smallDescription);
		$this->assertEquals(' hello world', $obj->fullDescription);

	}

	function testFullDescription () {
		$comment = "	/**
		* hello world
		*
		* Lorem ipsum
		* dolores
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals(' hello world', $obj->smallDescription);
		$this->assertEquals(' Lorem ipsum dolores', $obj->fullDescription);
	}

	function testAbstract() {
		$comment = "	/**
		* @abstract
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('', $obj->smallDescription);
		$this->assertEquals('', $obj->fullDescription);
		$this->assertTrue($obj->abstract);
	}

	function testAuthor() {
		$comment = "	/**
		* @author abc def
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('abc def', $obj->author);
	}

	function testGlobal() {
		$comment = "	/**
		* @global \$foo
		* @global \$bar
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('$foo', $obj->globals[0]);
		$this->assertEquals('$bar', $obj->globals[1]);
	}

	function testSinceSeeTodo() {
		$comment = "	/**
		* @since 2014
		* @see FooBar
		* @todo something sometime
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('2014', $obj->since);
		//$this->assertEquals('FooBar', $obj->see);
		$this->assertEquals('something sometime', $obj->todo[0]);
	}

	function testAccessStaticVersion() {
		$comment = "	/**
		* @access private
		* @static
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertTrue($obj->isPrivate);
		$this->assertTrue($obj->static);

		$comment = "	/**
		* @access public
		* @version 1.0
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertFalse($obj->isPrivate);
		$this->assertEquals('1.0', $obj->version);
	}

	function testParam() {
		$comment = "	/**
		* @param object lorem ipsum
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals(1 , count($obj->params));
		$o = $obj->params[0];
		$this->assertEquals('stdClass', $o->type);
		$this->assertEquals('lorem ipsum', $o->comment);

		$comment = "	/**
		* @param object lorem ipsum
		* @param string foo bar baz
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals(2 , count($obj->params));
		$o = $obj->params[0];
		$this->assertEquals('stdClass', $o->type);
		$this->assertEquals('lorem ipsum', $o->comment);
		$o = $obj->params[1];
		$this->assertEquals('string', $o->type);
		$this->assertEquals('foo bar baz', $o->comment);
	}

	function testExternalParam() {
		$comment = "	/**
		* @externalparam object \$foo lorem ipsum
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals(1 , count($obj->externalParams));
		$o = $obj->externalParams[0];
		$this->assertEquals('stdClass', $o->type);
		$this->assertEquals('foo', $o->name);
		$this->assertEquals('lorem ipsum', $o->comment);

		$comment = "	/**
		* @externalparam object \$foo lorem ipsum
		* @externalparam string \$bar foo bar baz
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals(2 , count($obj->externalParams));
		$o = $obj->externalParams[0];
		$this->assertEquals('stdClass', $o->type);
		$this->assertEquals('foo', $o->name);
		$this->assertEquals('lorem ipsum', $o->comment);
		$o = $obj->externalParams[1];
		$this->assertEquals('string', $o->type);
		$this->assertEquals('bar', $o->name);
		$this->assertEquals('foo bar baz', $o->comment);
	}

	function testVar() {
		$comment = "	/**
		* @var string lorem ipsum
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('string', $obj->type);
		$this->assertFalse($obj->optional);
		$this->assertFalse($obj->autoincrement);
		$this->assertEquals('lorem ipsum', $obj->description);

		$comment = "	/**
		* @var string lorem ipsum [OPTIONAL] [AUTOINCREMENT]
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('string', $obj->type);
		$this->assertTrue($obj->optional);
		$this->assertTrue($obj->autoincrement);
		$this->assertEquals('lorem ipsum', $obj->description);
	}

	function testReturn() {
		$comment = "	/**
		* @return
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('', $obj->return);
		$comment = "	/**
		* @return void
		*/";
		$obj = new testCommentClass();
		$parser = new IPReflectionCommentParser($comment, $obj);

		$this->assertEquals('void', $obj->return);
	}

}