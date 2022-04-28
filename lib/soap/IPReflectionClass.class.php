<?php

/**
 * An extended reflection/documentation class for classes.
 *
 * This class extends the reflectionClass class by also parsing the
 * comment for javadoc compatible @tags and by providing help
 * functions to generate a WSDL file. THe class might also
 * be used to generate a phpdoc on the fly
 *
 * @version 0.1
 *
 * @author David Kingma
 * @contributor Sylvain de Vathaire
 * @contributor Laurent Jouanneau
 * @extends reflectionClass
 */
class IPReflectionClass extends ReflectionClass
{
    /** @var string class name */
    public $classname = null;

    /** @var string */
    public $fullDescription = '';

    /** @var string */
    public $smallDescription = '';

    /** @var IPReflectionMethod[] */
    public $methods = array();

    /** @var IPReflectionProperty[] */
    public $properties = array();

    /** @var string */
    public $extends;

    /** @var string */
    private $comment = null;

    /**
     * Constructor.
     *
     * sets the class name and calls the constructor of the reflectionClass
     *
     * @param string The class name
     */
    public function __construct($classname)
    {
        $this->classname = $classname;
        parent::__construct($classname);

        $this->parseComment();
    }

    /**
     *Levert een array met alle methoden van deze class op.
     *
     * @param bool If the method should also return protected functions
     * @param bool If the method should also return private functions
     *
     * @return IPReflectionMethod[]
     */
    #[\ReturnTypeWillChange]
    public function getMethods($alsoProtected = true, $alsoPrivate = true, $alsoHerited = false)
    {
        $ar = parent::getMethods();
        foreach ($ar as $method) {
            if (substr($method->name, 0, 2) == '__' ||
                $method->isAbstract() ||
                $method->isConstructor() ||
                $method->isDestructor()) {
                continue;
            }
            $m = new IPReflectionMethod($this->classname, $method->name);
            if ((!$m->isPrivate() || $alsoPrivate) && (!$m->isProtected() || $alsoProtected) && (($m->getDeclaringClass()->name == $this->classname) || $alsoHerited)) {
                $this->methods[$method->name] = $m;
            }
        }
        ksort($this->methods);

        return $this->methods;
    }

    /**
     * Levert een array met variabelen van deze class op.
     *
     * @param bool If the method should also return protected properties
     * @param bool If the method should also return private properties
     *
     * @return IPReflectionProperty[]
     */
    #[\ReturnTypeWillChange]
    public function getProperties($alsoProtected = true, $alsoPrivate = true, $alsoHerited = false)
    {
        $ar = parent::getProperties();
        $this->properties = array();
        foreach ($ar as $property) {
            if ((!$property->isPrivate() || $alsoPrivate) && (!$property->isProtected() || $alsoProtected) && (($property->getDeclaringClass()->name == $this->classname) || $alsoHerited)) {
                try {
                    $p = new IPReflectionProperty($this->classname, $property->getName());
                    $this->properties[$property->name] = $p;
                } catch (ReflectionException $exception) {
                    echo 'Property error: '.$property->name."<br>\n";
                }
            }
        }
        ksort($this->properties);

        return $this->properties;
    }

    /**
     * read an extended annotation.
     *
     * @param $annotationName String the annotation name
     * @param $annotationClass String the annotation class
     *
     * @see IPhpDoc::getAnnotation()
     */
    public function getAnnotation($annotationName, $annotationClass = null)
    {
        return IPPhpDoc::getAnnotation($this->comment, $annotationName, $annotationClass);
    }

    /**
     * Gets all the usefull information from the comments.
     */
    private function parseComment()
    {
        $this->comment = $this->getDocComment();
        new IPReflectionCommentParser($this->comment, $this);
    }
}
