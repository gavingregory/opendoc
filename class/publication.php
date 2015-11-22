<?php
/**
 * A class that contains code for the Publication class
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */
  class Publication extends Siteaction
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
        $context->local()->addval('publications', R::find('publication'));
        return 'publication/index.twig';
    }

    public function handleview($context, $id)
    {
        $context->local()->addval('publication', R::load('publication', intval($id)));
        return 'publication/view.twig';
    }

    public function handlecreate($context)
    {

        if ($_SERVER['REQUEST_METHOD']  == 'POST')
        {
            if ( ($title = $context->postpar('title', '')) != '' &&
               ($description = $context->postpar('description', '')) != '' &&
               ($licence = $context->postpar('licence', '')) != '' &&
               ($typeid = $context->postpar('typeid', '')) != ''
             )
            {
                $u = R::dispense('publication');
                $u->title = $title;
                $u->description = $description;
                $u->licence = $licence;
                $u->typeid = $typeid;
                R::store($u);
                $this ->redirect('/publication');
            }
            else
            {
                $context->local()->addval('types', R::find('publicationtype'));
                $context->local()->addval('message', 'Something has gone wrong');
                return 'publication/create.twig';
            }
        }
        else
        {
            $context->local()->addval('types', R::find('publicationtype'));
            return 'publication/create.twig';
        }

    }

    public function handledelete($context, $id)
    {
        $bean = R::load('publication', intval($id));
        $context->local()->addval('type', $bean);
        if ($_SERVER['REQUEST_METHOD']  == 'POST')
        {
            R::trash($bean);
            $this->redirect('/publication');
        }
        else
        {
            echo('hmm...');
        }
        return 'publication/delete.twig';
    }

    public function handleupdate($context, $id)
    {
        $bean = R::load('publicationtype', $id);
        $context->local()->addval('type', $bean);
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            // update values
        }

        return 'publicationtype/update.twig';
    }

    public function redirect($url)
    {
        header('Location: ' . $url, true, 302);
        return 0;
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
