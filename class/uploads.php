<?php

/**
 * A class that contains code for the Publication class
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */

    class Uploads extends Siteaction
    {
        public function handle($context)
        {
            $path = 'uploads/'.$context->rest()[0];
            if (file_exists($path)) {
                $context->sendfile($path, $context->rest()[0]);
            } else {
                return 'filenotfound.twig';
            }
        }
    }

?>
