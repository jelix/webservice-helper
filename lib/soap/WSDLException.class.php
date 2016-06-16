<?php

/**
 * Exception class which can be thrown by
 * the WSDLStruct class.
 */
class WSDLException extends Exception
{
    /**
     * @param string The error message
     */
    public function __construct($msg, $code)
    {
        $this->msg = $msg;
        parent::__construct($msg, $code);
    }
    /**
     */
    public function Display()
    {
        print 'Error creating WSDL document:'.$this->msg;
        //var_dump(debug_backtrace());
    }
}
