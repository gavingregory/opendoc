<?php
/**
 * A class that contains code and functionality relating to file handling.
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
    class FileHandler
    {

/**
 * Global static variables for file uploads
 * uploads/ permissions: sudo chmod 777 -R .
 */
        public static $uploadsdir = "uploads/";
        public static $uploadslimit = 10485760; //10MB
        public static $uploadsfiletypes = array('zip', 'docx', 'doc', 'dat', 'txt', 'tar.gz', 'pdf', 'png', 'jpg', 'jpeg');

        public static function checkfile($file)
        {
            try {
                $targetfile = FileHandler::$uploadsdir . basename($file['name']);
                $filetype = pathinfo($targetfile, PATHINFO_EXTENSION);

                // Undefined | Multiple Files | $_FILES Corruption Attack
                // If this request falls under any of them, treat it invalid.
                if (!isset($file['error']) || is_array($file['error']))
                {
                    return 'Invalid parameters'; // do not continue, we have no correct file
                }

                // You should also check filesize here.
                if ($file['size'] > FileHandler::$uploadslimit)
                {
                    return 'Exceeded file size limit.';
                }

                // check if the filetype is allowed
                $typematch = false;
                foreach (FileHandler::$uploadsfiletypes as $type)
                {
                    if ($filetype == $type) $typematch = true;
                }
                if (!$typematch)
                {
                    return 'File type is not supported.';
                }

                // check if the file exists
                if (file_exists($targetfile))
                { // return here so we don't try and save file, although this should never happen
                    return 'Target file exists';
                }
                return 0;
            } catch (RuntimeException $e)
            {
                throw $e;
            }
        }

        public static function uploadfile($file, $id)
        {
            // Create a unique filename for this file
            $filename = FileHandler::$uploadsdir.$id.'_'.$file['name'];
            $status = move_uploaded_file($file['tmp_name'], $filename );

            if ($status == 1)
            { // success
                return $filename;
            }
            else
            { // failure
                return 0;
            }
        }

        public static function deletefile($filename)
        {
            unlink(FileHandler::$uploadsdir.$filename);
        }

    }
?>
