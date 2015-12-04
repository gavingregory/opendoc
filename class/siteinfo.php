<?php
/**
 * A class that contains code to return info needed in various places on the site
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2014 Newcastle University
 *
 */
    class SiteInfo
    {

/**
 * Global static variables for file uploads
 * uploads/ permissions: sudo chmod 777 -R .
 */
        public static $uploadsdir = "uploads/";
        public static $uploadslimit = 10485760; //10MB
        public static $uploadsfiletypes = array('zip', 'docx', 'doc', 'dat', 'txt', 'tar.gz', 'pdf', 'png', 'jpg', 'jpeg');

/**
 * Get all the user beans
 *
 * @return array
 */
        public function users()
        {
            return R::findAll('user', 'order by login');
        }
/**
 * Get all the page beans
 *
 * @return array
 */
        public function pages()
        {
            return R::findAll('page', 'order by name');
        }
/**
 * Get all the Rolename beans
 *
 * @return array
 */
        public function roles()
        {
            return R::findAll('rolename', 'order by name');
        }
/**
 * Get all the Rolecontext beans
 *
 * @return array
 */
        public function contexts()
        {
            return R::findAll('rolecontext', 'order by name');
        }

/**
 * Parses form input for malicious content
 *
 * @param	string	$data	The unsafe data to parse
 *
 * @return	string	The safe string
 */

        public function test_form_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

    }
?>
