<?php

class ValidationException extends Exception
{
    private $errors;

    public function __construct($message = null,
                                $code = 0,
                                $errors = array())
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function GetErrors()
    {
        return $this->errors;
    }
}

?>
