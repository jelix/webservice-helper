<?php 
/**
 * Exception class which can be thrown by
 * the WSDLStruct class.
 */
class WSDLException extends Exception { 
 	/** 
 	 * @param string The error message
 	 * @return void 
 	 */
	function __construct($msg, $code) { 
		$this->msg = $msg;
		parent::__construct($msg, $code);
	} 
 	/** 
 	 * @return void 
 	 */
 	function Display() { 
		print "Error creating WSDL document:".$this->msg; 
		//var_dump(debug_backtrace());
	} 
} 
