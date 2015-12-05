<?php
/**
 * A class that contains utility functions
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
    class Utilities
    {

        /**
         * Parses form input for malicious content
         *
         * @param	string	$data	The unsafe data to parse
         *
         * @return	string	The safe string
         */

                public function sanitise($data)
                {
                    $data = trim($data);
                    $data = stripslashes($data);
                    $data = htmlspecialchars($data);
                    return $data;
                }
    }
?>
