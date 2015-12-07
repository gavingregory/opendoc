<?php

/**
 * An Exception for Validation errors.
 * This class is required as it contains an array of validation errors that can
 * then be obtained from whoever catched the exception, and can handle them
 * appropriately.
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */

class ValidationException extends Exception
{
/**
 * @var array		An array of validation errors
 */
    private $errors;

/**
 * Constructor for the exception. This constructor calls the base constructor
 * and initialises the array.
 *
 * @param string	$message	The error message
 * @param integer	$code	The exception code
 * @param array	$errors	The error array (defaults to empty array)
 *
 */
    public function __construct($message = null,
                                $code = 0,
                                $errors = array())
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

/**
 * Gets the errors associated with this validation exception.
 *
 * @return array	$errors	The error array (defaults to empty array)
 *
 */
    public function GetErrors()
    {
        return $this->errors;
    }
}

?>
