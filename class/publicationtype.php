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
        $action = getaction($rest);
        switch ($action['route'])
        {
        case 'index':
        $this->handleindex($context);
        break;
        case 'view':
        $this->handleview($context, $action['parameter']);
        break;
        case 'update':
        $this->handleupdate($context, $action['parameter']);
        break;
        case 'delete':
        $this->handledelete($context, $action['parameter']);
        break;
        }

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

    public function handleview($context, $id)
    {
        echo('view id: '.$id);
    }

    public function handlecreate($context)
    {
        echo('Create!');
    
    }

    public function handledelete($context, $id)
    {
        echo('delete id: '.$id);
    
    }

    public function handleupdate($context, $id)
    {
        echo('update id: '.$id);
    }

    public function getaction($rest)
    {
        if (!is_array($rest) || !is_set($rest[0]))
        {
            throw new Exception('getaction() function requires an array.');
        }
        $ret = Array();
        if (is_numeric($rest[0])
        {
            if (is_set($rest[1])
            {
                $ret['route'] = $rest[1];
                $ret['parameter'] = $rest[0];
            }
            else
            {
                $ret['route'] = 'view';
                $ret['parameter'] = $rest[0];
            }
        }
        elseif ($rest[0] = '')
        {
            $ret['route'] = 'index';
        }
        else
        {
            $ret['route'] = $rest[0];
        }
        return $ret;
    }

  }
?>
