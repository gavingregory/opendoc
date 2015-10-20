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
        $action = $this->getaction($context->rest());
        switch ($action['route'])
        {
        case 'index':
        return $this->handleindex($context);
        break;
        case 'view':
        return $this->handleview($context, $action['parameter']);
        break;
        case 'update':
        return $this->handleupdate($context, $action['parameter']);
        break;
        case 'delete':
        return $this->handledelete($context, $action['parameter']);
        break;
        case 'create':
        return $this->handlecreate($context);
        break;
        default:
        throw new Exception('unhandled route.');
        break;
        }
    }

    public function handleindex($context)
    {
        $context->local()->addval('types', R::find('publicationtype'));
        return 'publicationtype.index.twig';
    }

    public function handleview($context, $id)
    {
        $context->local()->addval('type', R::load('publicationtype', $id));
        return 'publicationtype.view.twig';
    }

    public function handlecreate($context)
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
            return 'publicationtype.create.twig';
        }
    }

    public function handledelete($context, $id)
    {
        $context->local()->addval('type', R::load('publicationtype', intval($id)));
        return 'publicationtype.delete.twig';
    }

    public function handleupdate($context, $id)
    {
        //$bean = R::findOne('publicationtype', 'id = ?', [$id]);
        //$context->local()->addval('type', $bean));
        $context->local()->addval('type', R::load('publicationtype', $id));
        return 'publicationtype.update.twig';
    }

    public function getaction($rest)
    {
        if (!is_array($rest) || !isset($rest[0]))
        {
            throw new Exception('getaction() function requires an array.');
        }
        $ret = Array();
        if (is_numeric($rest[0]))
        {
            if (isset($rest[1]))
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
        elseif ($rest[0] == '')
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
