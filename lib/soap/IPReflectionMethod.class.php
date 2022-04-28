<?php

/**
 * An extended reflection/documentation class for class methods.
 *
 * This class extends the ReflectionMethod class by also parsing the
 * comment for javadoc compatible @tags and by providing help
 * functions to generate a WSDL file. The class might also
 * be used to generate a phpdoc on the fly
 *
 * @version 0.1
 *
 * @author David Kingma
 * @contributor Laurent Jouanneau
 */
class IPReflectionMethod extends ReflectionMethod
{
    /** @var string class name */
    public $classname;

    /** @var string The return type for this method	 */
    public $return = '';

    /** @var reflectionParameter[] Associative array with reflectionParameter objects */
    public $parameters = array();

    /** @var string */
    public $fullDescription = '';

    /** @var string */
    public $smallDescription = '';

    /** @var string */
    public $throws = '';

    /**
     * Constructor which calls the parent constructor and makes sure the comment
     * of the method is parsed.
     *
     * @param string The class name
     * @param string The method name
     */
    public function __construct($class, $method)
    {
        $this->classname = $class;
        parent::__construct($class, $method);
        $this->parseComment();
    }

    /**
     * Returns the full function name, including arguments.
     *
     * @return string
     */
    public function getFullName()
    {
        $args = $this->getParameters();
        $argstr = '';

        foreach ((array) $args as $arg) {
            if ($argstr != '') {
                $argstr .= ', ';
            }
            $argstr .= $arg->type.' $'.$arg->name;
        }

        return $this->return.' '.$this->name.'('.$argstr.')';
    }

    /**
     * Returns an array with parameter objects, containing type info etc.
     *
     * @return ReflectionParameter[] Associative array with parameter objects
     */
    #[\ReturnTypeWillChange]
    public function getParameters()
    {
        $this->parameters = array();
        $ar = parent::getParameters();
        foreach ((array) $ar as $i => $parameter) {
            $parameter->type = '';
            try {
                $className = $this->compatGetClass($parameter);
                if ($className) {
                    $parameter->type = $className;
                }
            } catch (Exception $e) {
            }
            if ($parameter->type == '') {
                if ($this->compatGetType($parameter, 'array')) {
                    $parameter->type = 'array';
                } elseif ($this->compatGetType($parameter, 'callable')) {
                    $parameter->type = 'function';
                } elseif (isset($this->params) && isset($this->params[$i])) {
                    $parameter->type = $this->params[$i]->type;
                } else {
                    $parameter->type = 'mixed';
                }
            }
            $this->parameters[$parameter->name] = $parameter;
        }
        if (isset($this->externalParams)) {
            foreach ($this->externalParams as $param) {
                $this->parameters[$param->name] = $param;
            }
        }

        return $this->parameters;
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
     * Parses the comment and adds found properties to this class.
     */
    private function parseComment()
    {
        $this->comment = $this->getDocComment();
        new IPReflectionCommentParser($this->comment, $this);
    }


    private function compatGetType($reflectionParameter, $expectedType)
    {
        if (!method_exists($reflectionParameter, 'getType')) {
            // compatibility with PHP 5
            return $reflectionParameter->isArray();
        }

        $reflectionType = $reflectionParameter->getType();

        if (!$reflectionType) return false;

        $types = $reflectionType instanceof ReflectionNamedType
            ? [$reflectionType]
            : $reflectionType->getTypes();

        return in_array($expectedType, array_map(function(ReflectionNamedType $t) {
            return $t->getName();
        }, $types));

    }


    private function compatGetClass($reflectionParameter)
    {
        if (!method_exists($reflectionParameter, 'getType')) {
            // compatibility with PHP 5
            $ref = $reflectionParameter->getClass();
            if ($ref) {
                return $ref->getName();
            }
            return '';
        }

        $reflectionType = $reflectionParameter->getType();

        if (!$reflectionType) return '';

        $types = $reflectionType instanceof ReflectionNamedType
            ? [$reflectionType]
            : $reflectionType->getTypes();

        foreach ($types as $type) {
            if (!$type->isBuiltin()) {
                return $type->getName();
            }
        }
        return '';

    }


}
