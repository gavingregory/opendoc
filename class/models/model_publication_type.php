<?php
/**
 * A model class for the RedBean object PublicationType
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
/**
 * A class implementing a RedBean model for PublicationType beans
 */
    class Model_PublicationType extends RedBean_SimpleModel
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

    }
?>
