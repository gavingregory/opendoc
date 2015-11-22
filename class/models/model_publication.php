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
 * Return description object
 *
 * @return object
 */

        public function type()
        {
            return $this->bean->type;
        }

    }
?>
