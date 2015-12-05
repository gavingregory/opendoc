<?php
/**
 * A model class for the RedBean object Publication
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
/**
 * A class implementing a RedBean model for Publication beans
 */
    class Model_Publication extends RedBean_SimpleModel
    {

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



    }
?>
