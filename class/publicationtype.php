<?php
/**
 * A class that contains code for the Profile class
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
  class PublicationType extends Siteaction
  {
/**
 * Handle profile operations /publicationtype/xxxx
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
    public function handle($context)
    {
        if ( ($name = $context->postpar('name', '')) != '' &&
             ($description = $context->postpar('description', '')) != ''
           )
        { # there is a post
            $u = R::dispense('publicationtype');
            $u->name = $name;
            $u->description = $description;
            R::store($u);
            $context->local()->addval('types', R::find('publicationtype'));
            return 'publicationtype.twig';
        }
        $context->local()->addval('types', R::find('publicationtype'));

        return 'publicationtype.twig';
    }
  }
?>
