<?php

/**
 * Class that generates a WSDL file and creates documentation
 * for your webservices.
 *
 * Patch by Shawn Cook (Shawn@itbytez.com) for the useWSDLCache option
 *
 * @author David Kingma
 *
 * @version 1.5
 */
class WSHelper
{
    private $uri;
    private $class = null; //IPReflectionClass object
    private $name; //class name
    private $persistence = SOAP_PERSISTENCE_SESSION;
    private $wsdlfile; //wsdl file name
    private $server; //soap server object

    public $actor;
    public $structureMap = array();
    public $classNameArr = array();
    public $wsdlFolder; //WSDL cache folder
    public $useWSDLCache = true;

    public $type = SOAP_RPC;
    public $use = SOAP_LITERAL;

    public $docTemplate = '';
    /**
     * Constructor.
     *
     * @param string The Uri name
     */
    public function __construct($uri, $class = null)
    {
        $this->uri = $uri;
        $this->setWSDLCacheFolder($_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']).'/wsdl/');

        if ($class) {
            $this->setClass($class);
        }
    }

    /**
     * Adds the given class name to the list of classes
     * to be included in the documentation/WSDL/Request handlers.
     *
     * @param string
     */
    public function setClass($name)
    {
        $this->name = $name;
        $this->wsdlfile = $this->wsdlFolder.$this->name.'.wsdl';
    }

    public function setWSDLCacheFolder($folder)
    {
        $this->wsdlFolder = $folder;
        //reset wsdlfile
        $this->wsdlfile = $this->wsdlFolder.$this->name.'.wsdl';
    }
    /**
     * Sets the persistence level for the soap class.
     */
    public function setPersistence($persistence)
    {
        $this->persistence = $persistence;
    }

    /**
     * Handles everything. Makes sure the webservice is handled,
     * documentations is generated, or the wsdl is generated,
     * according to the page request.
     */
    public function handle()
    {
        if (substr($_SERVER['QUERY_STRING'], -4) == 'wsdl') {
            $this->showWSDL();

            return;
        }
        if ($this->isSoapRequest()) {
            $this->handleRequest();
        } else {
            $this->createDocumentation();
        }
    }

    private function isSoapRequest()
    {
        $contentType = '';
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $contentType = $_SERVER['CONTENT_TYPE'];
        } elseif (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
            $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
        }

        if (strpos($contentType, 'text/xml') === 0 ||
            strpos($contentType, 'application/soap+xml') === 0) {
            return true;
        }

        if (isset($_SERVER['HTTP_SOAPACTION'])) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the current WSDL is up-to-date, regenerates if necessary and outputs the WSDL.
     */
    public function showWSDL()
    {
        //check if it's a legal webservice class
        if (!in_array($this->name, $this->classNameArr)) {
            throw new Exception('No valid webservice class.');
        }

        header('Content-type: text/xml');
        if ($this->useWSDLCache && file_exists($this->wsdlfile)) {
            readfile($this->wsdlfile);
        } else {
            //make sure to refresh PHP WSDL cache system
            ini_set('soap.wsdl_cache_enabled', 0);
            echo $this->createWSDL();
        }
    }

    private function createWSDL()
    {
        $this->class = new IPReflectionClass($this->name);
        $wsdl = new WSDLStruct($this->uri, 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?class='.$this->name, $this->type, $this->use);
        $wsdl->setService($this->class);
        $gendoc = $wsdl->generateDocument();
        $fh = fopen($this->wsdlfile, 'w+');
        fwrite($fh, $gendoc);
        fclose($fh);

        return $gendoc;
    }

    /**
     * Lets the native PHP5 soap implementation handle the request
     * after registrating the class.
     */
    private function handleRequest()
    {
        //check if it's a legal webservice class
        if (!in_array($this->name, $this->classNameArr)) {
            throw new Exception('No valid webservice class.');
        }

        //check cache
        if (!file_exists($this->wsdlfile)) {
            $this->createWSDL();
        }

        $options = array('soap_version' => SOAP_1_1,
                         'actor' => $this->actor,
                         'classmap' => $this->structureMap, );

        header('Content-type: text/xml');
        $this->server = new SoapServer($this->wsdlfile, $options);
        $this->server->setClass($this->name);
        $this->server->setPersistence($this->persistence);

        use_soap_error_handler(true);
        $this->server->handle();
    }

    /**
     * @param string code
     * @param string string
     * @param string actor
     * @param mixed details
     * @param string name
     */
    public function fault($code, $string, $actor, $details, $name = '')
    {
        return $this->server->fault($code, $string, $actor, $details, $name);
    }

    /**
     * Generates the documentations for the webservice usage.
     *
     * @TODO: "int", "boolean", "double", "float", "string", "void"
     *
     * @param string Template filename
     */
    public function createDocumentation($template = '')
    {
        if ($template == '') {
            $template = $this->docTemplate;
        }
        if ($template == '') {
            throw new WSException('No template file to generate documentation');
        }
        if (!is_file($template)) {
            throw new WSException("Could not find the template file: '$template'");
        }
        $this->class = new IPReflectionClass($this->name);
        $xtpl = new IPXSLTemplate($template);
        $documentation = array();
        $documentation['menu'] = array();
        //loop menu items
        sort($this->classNameArr);//ff sorteren
        foreach ($this->classNameArr as $className) {
            $documentation['menu'][] = new IPReflectionClass($className);
        }

        if ($this->class) {
            $this->class->properties = $this->class->getProperties(false, false);
            $this->class->methods = $this->class->getMethods(false, false);
            foreach ((array) $this->class->methods as $method) {
                $method->params = $method->getParameters();
            }

            $documentation['class'] = $this->class;
        }
        echo $xtpl->execute($documentation);
    }
}
