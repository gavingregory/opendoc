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
            $context->sendfile('uploads/'.$context->rest()[0], $context->rest()[0]);
        }
    }

?>
