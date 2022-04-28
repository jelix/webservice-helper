<?PHP

/**
 * The base phpdoc class searches for available classes.
 *
 * @version 0.1
 *
 * @author David Kingma
 */
class IPPhpDoc
{
    /** @var IPReflectionClass[] Array with available classes */
    public $classes = array();

    /** @var IPReflectionClass The current class */
    public $class = '';

    /**
     * Constructor, initiates the getClasses() method.
     */
    public function __construct()
    {
        $this->getClasses();
    }

    /** Sets the current class
     * @param string The class name
     */
    public function setClass($class)
    {
        $this->class = new IPReflectionClass($class);
    }
    /**
     * Haalt alle geladen classes op die 'custom zijn.
     *
     * @return IPReflectionClass[]
     */
    public function getClasses()
    {
        $ar = get_declared_classes();
        foreach ($ar as $class) {
            $c = new reflectionClass($class);
            if ($c->isUserDefined()) {
                //add only when class is user-defined
                $this->classes[$class] = new IPReflectionClass($class);
            }
        }
        ksort($this->classes);

        return $this->classes;
    }
    /**
     * Generates the documentation page with all classes, methods etc.
     *
     * @param string Template file (optional)
     *
     * @return string
     */
    public function getDocumentation($template = null)
    {
        if ($template === null) {
            $template = __DIR__.'/../../doc/templates/docclass.xsl';
        }
        if (!is_file($template)) {
            throw new WSException("Could not find the template file: '$template'");
        }
        $xtpl = new IPXSLTemplate($template);
        $documentation = array();
        //loop menu items
        $documentation['menu'] = $this->getClasses();

        if ($this->class) {
            if ($this->class->isUserDefined()) {
                $this->class->properties = $this->class->getProperties(false, false);
                $this->class->methods = $this->class->getMethods(false, false);
                foreach ((array) $this->class->methods as $method) {
                    $method->params = $method->getParameters();
                }
            } else {
                $documentation['fault'] = 'Native class';
            }
            $documentation['class'] = $this->class;
        }
        echo $xtpl->execute($documentation);
    }

    /**
     * read extended annotation.
     *
     * An extended annotation is an annotation followed by
     * an associative array notation like @something('foo'=>'bar', 'bla'=>'baz')
     *
     * The value of the annotation is stored as properties of an instance of
     * the given $annotationClass.
     *
     * @param $comment String The doccomment
     * @param $annotationName String the annotation name
     * @param $annotationClass String the annotation class
     */
    public static function getAnnotation($comment, $annotationName, $annotationClass = null)
    {
        if (!$annotationClass) {
            $annotationClass = $annotationName;
        }
        $start = 0;
        if ($start = stripos($comment, '@'.$annotationName)) {
            $obi = new $annotationClass();
            $start = strpos($comment, '(', $start);
            $end = strpos($comment, ')', $start);
            $propString = substr($comment, $start, ($end - $start) + 1);
            $eval = "return Array$propString;";
            $arr = @eval($eval);
            if ($arr === false) {
                throw new Exception("Error parsing annotation: $propString");
            }

            foreach ((Array) $arr as $name => $value) {
                $obi->$name = $value;
            }

            return $obi;
        }
        throw new Exception("Cannot find annotation @$annotationName ($start, $end) in the comment: $comment ");
    }
}
