<?php

    //include 'class/validationexception.php';

/**
 * A class implementing a RedBean model for Publication beans
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
    class Model_Publication extends RedBean_SimpleModel
    {

        private $errors;

        private function adderror($property, $error)
        {
            if (!isset($this->errors[$property]))
            {
                $this->errors[$property] = array();
            }
            array_push($this->errors[$property], $error);
        }

        public function update()
        {
            // begin a new transaction
            R::begin();

            $this->errors = array();

            // check required fields
            if (empty($this->name))         $this->adderror('name', 'Cannot be empty');
            if (empty($this->description))  $this->adderror('description', 'Cannot be empty');
            if (empty($this->authors))      $this->adderror('authors', 'Cannot be empty');
            if (empty($this->tags))         $this->adderror('tags', 'Cannot be empty');
            if (empty($this->data) && !($this->isfile)) $this->adderror('data', 'Cannot be empty');

            if (!is_string($this->name))         $this->adderror('name', 'Must be a valid string.');
            if (!is_string($this->description))  $this->adderror('description', 'Must be a valid string.');
            if (!is_string($this->authors))      $this->adderror('authors', 'Must be a valid string.');
            if (!is_string($this->tags))         $this->adderror('tags', 'Must be a valid string.');
            if (!is_string($this->data) && !($this->isfile)) $this->adderror('data', 'Must be a valid string.');

            //must have exactly one type selected
            $count = 0;
            if ($this->issourcecode) $count++;
            if ($this->isdata) $count++;
            if ($this->isapp) $count++;
            if ($this->isdocument) $count++;
            if ($count != 1)   $this->adderror('type', 'Cannot be empty');

        }

        public function after_update()
        {
            echo count($this->errors);
            if (count($this->errors) > 0)
            {
                R::rollback();
                throw new ValidationException('Validation exception.', 0, $this->errors);
            }
        }

/**
 * Return id object
 *
 * @return object
 */
        public function id()
        {
            return $this->bean->id;
        }

/**
 * Return name object
 *
 * @return object
 */
        public function name()
        {
            return $this->bean->name;
        }

/**
 * Return description object
 *
 * @return object
 */

        public function description()
        {
            return $this->bean->description;
        }

/**
 * Return the comma separated list of authors
 *
 * @return object
 */

public function authors()
{
    return $this->bean->authors;
}

/**
 * Return the comma separated list of tags
 *
 *@return object
 */

        public function tags()
        {
            return $this->bean->tags;
        }

/**
 * Return a boolean value of whether this publication is a document
 *
 * @return object
 */

        public function isdocument()
        {
            return $this->bean->isdocument;
        }

/**
 * Return a boolean value of whether this publication is a file
 *
 * @return object
 */

        public function isapp()
        {
            return $this->bean->isapp;
        }

/**
 * Return a boolean value of whether this publication is a data
 *
 * @return object
 */

        public function isdata()
        {
            return $this->bean->isdata;
        }

/**
 * Return a boolean value of whether this publication is source code
 *
 * @return object
 */
        public function issourcecode()
        {
            return $this->bean->issourcecode;
        }

/**
 * Returns the data associated with this document. The type of this data
 * depends on the CONTEXT of the document. i.e. if an app, this would be a
 * github url.
 *
 * @return object
 */

        public function data()
        {
            return $this->bean->data;
        }

/**
 * Returns a flag whether the data is a file or not
 *
 * @return object
 */

        public function isfile()
        {
            return $this->bean->isfile;
        }

    }
?>
