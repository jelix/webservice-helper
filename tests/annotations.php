<?php

class testExtendedAnnotationContainer
{
    const TYPE_PLAIN = 1;
    const TYPE_HTML = 2;
    public $type;
    public $length;
}

/**
 * @ann1('me'=>'you');
 */
class extendedAnnotationTestClass
{
    /**
     * @var string
     * @Controller(type => testExtendedAnnotationContainer::TYPE_PLAIN, length => 100)
     */
    public $propertyA;

    /**
     * @var string
     * @Controller(type => testExtendedAnnotationContainer::TYPE_HTML, length => 215)
     */
    public function methodB()
    {
        return 'aap';
    }
}

class extendedAnnotationTest extends PHPUnit_Framework_TestCase
{
    public function testClassAnnotations()
    {
        $rel = new IPReflectionClass('extendedAnnotationTestClass');
        $ann = $rel->getAnnotation('ann1', 'stdClass');

        $this->assertInternalType('object', $ann);
        $this->assertInstanceOf('stdClass', $ann);
        $this->assertEquals('you', $ann->me);
    }

    public function testPropertyAnnotations()
    {
        $rel = new IPReflectionClass('extendedAnnotationTestClass');
        $properties = $rel->getProperties();
        $property = $properties['propertyA'];

        $ann = $property->getAnnotation('Controller', 'testExtendedAnnotationContainer');

        $this->assertInternalType('object', $ann);
        $this->assertInstanceOf('testExtendedAnnotationContainer', $ann);
        $this->assertEquals(testExtendedAnnotationContainer::TYPE_PLAIN, $ann->type);
        $this->assertEquals(100, $ann->length);
    }

    public function testMethodAnnotations()
    {
        $rel = new IPReflectionClass('extendedAnnotationTestClass');
        $methods = $rel->getMethods();
        $method = $methods['methodB'];

        $ann = $method->getAnnotation('Controller', 'testExtendedAnnotationContainer');

        $this->assertInternalType('object', $ann);
        $this->assertInstanceOf('testExtendedAnnotationContainer', $ann);
        $this->assertEquals(testExtendedAnnotationContainer::TYPE_HTML, $ann->type);
        $this->assertEquals(215, $ann->length);
    }
}
