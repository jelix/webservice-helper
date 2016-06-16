<?php

/**
 * Exception class which can be thrown by
 * the WSHelper class.
 */
class WSException extends Exception
{
    /**
     * @param string The error message
     */
    public function __construct($msg)
    {
        $this->msg = $msg;
        parent::__construct($msg, 1);
    }
    /**
     */
    public function Display()
    {
        echo $this->msg;
    }
}
