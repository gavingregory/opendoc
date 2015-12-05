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
            $errors = array();

            try {
                $targetfile = FileHandler::$uploadsdir . basename($file['name']);
                $filetype = pathinfo($targetfile, PATHINFO_EXTENSION);

                // Undefined | Multiple Files | $_FILES Corruption Attack
                // If this request falls under any of them, treat it invalid.
                if (!isset($file['error']) || is_array($file['error']))
                {
                    array_push($errors, 'Invalid parameters');
                    return $errors; // do not continue, we have no correct file
                }

                // Check $file['error'] value.
                switch ($file['error'])
                {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        array_push($errors, 'No file sent.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        array_push($errors, 'Exceeded file size limit.');
                    default:
                        array_push($errors, 'Unknown errors.');
                }

                // You should also check filesize here.
                if ($file['size'] > FileHandler::$uploadslimit)
                {
                    array_push($errors, 'Exceeded file size limit.');
                }

                // check if the filetype is allowed
                $typematch = false;
                foreach (FileHandler::$uploadsfiletypes as $type)
                {
                    if ($filetype == $type) $typematch = true;
                }
                if (!$typematch)
                {
                    array_push($errors, 'File type is not supported.');
                }

                // check if the file exists
                if (file_exists($targetfile))
                {
                    array_push($errors, 'Target file exists.');
                    return $errors; // return here so we don't try and save file
                }
                return $errors;
            } catch (RuntimeException $e)
            {
                array_push($errors, 'There has been a runtime exception.');
            }
        }

        public static function uploadfile($file, $id)
        {
            // You should name it uniquely.
            // DO NOT USE $file['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from its binary data.
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

        }

    }
?>
