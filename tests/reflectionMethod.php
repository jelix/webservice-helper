<?php

class testParamMethod
{
}
class testParamMethod2
{
}

class testReflectionMethodClass
{
    /** @var string The return type for this method	 */
    public $return = '';

    public function meth1()
    {
    }

    public function meth2($param)
    {
    }

    public function meth3(testParamMethod $paramObj, array $arr)
    {
    }

    /**
     * @param testParamMethod lorem1 ipsum1
     */
    public function meth4($paramObj, $arr)
    {
    }

    /**
     * @param testParamMethod lorem1 ipsum1
     * @param array lorem2 ipsum2
     */
    public function meth5($paramObj, $arr)
    {
    }

    /**
     * @param testParamMethod lorem1 ipsum1
     * @param array lorem2 ipsum2
     */
    public function meth6(testParamMethod2 $paramObj, $arr)
    {
    }

    /**
     * @externalparam integer foo
     */
    public function meth7()
    {
    }

    /**
     * @param testParamMethod lorem1 ipsum1
     * @externalparam integer $foo something
     *
     * @param array lorem2 ipsum2
     */
    public function meth8(testParamMethod2 $paramObj, $arr)
    {
    }
}

class reflectionMethodTests  extends \PHPUnit\Framework\TestCase
{
    public function testNoParameter()
    {
        $ref = new IPReflectionMethod('testReflectionMethodClass', 'meth1');
        $parameters = $ref->getParameters();
        $this->assertEquals(0, count($parameters));
    }

    public function testOneParameter()
    {
        $ref = new IPReflectionMethod('testReflectionMethodClass', 'meth2');
        $parameters = $ref->getParameters();
        $this->assertArrayHasKey('param', $parameters);
        $p = $parameters['param'];
        $this->assertEquals('mixed', $p->type);
    }

    public function testTwoTypedParameters()
    {
        $ref = new IPReflectionMethod('testReflectionMethodClass', 'meth3');
        $parameters = $ref->getParameters();
        $this->assertArrayHasKey('paramObj', $parameters);
        $this->assertArrayHasKey('arr', $parameters);
        $p = $parameters['paramObj'];
        $this->assertEquals('testParamMethod', $p->type);
        $p = $parameters['arr'];
        $this->assertEquals('array', $p->type);
    }

    public function testTwoParametersWithOneCommented()
    {
        $ref = new IPReflectionMethod('testReflectionMethodClass', 'meth4');
        $parameters = $ref->getParameters();
        $this->assertArrayHasKey('paramObj', $parameters);
        $this->assertArrayHasKey('arr', $parameters);
        $p = $parameters['paramObj'];
        $this->assertEquals('testParamMethod', $p->type);
        $p = $parameters['arr'];
        $this->assertEquals('mixed', $p->type);
    }

    public function testTwoCommentedParameters()
    {
        $ref = new IPReflectionMethod('testReflectionMethodClass', 'meth5');
        $parameters = $ref->getParameters();
        $this->assertArrayHasKey('paramObj', $parameters);
        $this->assertArrayHasKey('arr', $parameters);
        $p = $parameters['paramObj'];
        $this->assertEquals('testParamMethod', $p->type);
        $p = $parameters['arr'];
        $this->assertEquals('array', $p->type);
    }

    public function testTwoCommentedTypedParameters()
    {
        $ref = new IPReflectionMethod('testReflectionMethodClass', 'meth6');
        $parameters = $ref->getParameters();
        $this->assertArrayHasKey('paramObj', $parameters);
        $this->assertArrayHasKey('arr', $parameters);
        $p = $parameters['paramObj'];
        $this->assertEquals('testParamMethod2', $p->type);
        $p = $parameters['arr'];
        $this->assertEquals('array', $p->type);
    }

    public function testExternalAndStaticParameters()
    {
        $ref = new IPReflectionMethod('testReflectionMethodClass', 'meth8');
        $parameters = $ref->getParameters();
        $this->assertArrayHasKey('paramObj', $parameters);
        $this->assertArrayHasKey('arr', $parameters);
        $this->assertArrayHasKey('foo', $parameters);
        $p = $parameters['paramObj'];
        $this->assertEquals('testParamMethod2', $p->type);
        $p = $parameters['arr'];
        $this->assertEquals('array', $p->type);
        $p = $parameters['foo'];
        $this->assertEquals('integer', $p->type);
    }
}
