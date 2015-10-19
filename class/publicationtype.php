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
      $u = R::dispense('publicationtype');
      $u->typename = 'something';
      R::store($u);
      return 'publicationtype.twig';
    }
  }
?>
