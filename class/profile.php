<?php
/**
 * A class that contains code for the Profile class
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
  class Profile extends Siteaction
  {
/**
 * Handle profile operations /profile/xxxx
 *
 * @param object	$context	The context object for the site
 *
 * @return string	A template name
 */
    public function handle($context)
    {
      if (($email = $context->postpar('email', '')) != '')
      { # there is a post
        $user = $context->user();
        $user->email = $email;
        R::store($user);
        $context->local()->addval('done', TRUE);
      }
      return 'profile.twig';
    }
  }
?>
