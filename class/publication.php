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
 * Handle profile operations /publication/xxxx
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
        case 'type':
        return $this->handletype($context, $action['parameter']);
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

    public function handletype($context, $type)
    {
        if ($type == 'document' || $type == 'data' || $type == 'sourcecode' || 'app')
        {
            $context->local()->addval('publications', R::find('publication', 'is'.$type.'=1'));
            return 'publication/index.twig';
        }
        else
        {
            throw new Exception('unhandled type.');
        }
    }

    public function handlecreate($context)
    {

        if ($_SERVER['REQUEST_METHOD']  == 'POST')
        {
            if ( ($name = $context->postpar('name', '')) != '' &&
               ($description = $context->postpar('description', '')) != '' &&
               ($licence = $context->postpar('licence', '')) != '' &&
               ($authors = $context->postpar('authors', '')) != '' &&
               ($type = $context->postpar('type', '')) != '' &&
               ($tags = $context->postpar('tags', '')) != '' &&
               ($data = $context->postpar('data', '')) != ''
             )
            {
                $u = R::dispense('publication');
                $u->name = $name;
                $u->description = $description;
                $u->licence = $licence;
                $u->authors = $authors;
                $u->tags = $tags;
                $u->isdocument = false;
                $u->isapp = false;
                $u->isdata = false;
                $u->issourcecode = false;
                $u->data = $data;

                switch ($type)
                {
                case 'isdocument':
                $u->isdocument = true;
                break;
                case 'isapp':
                $u->isapp = true;
                break;
                case 'isdata':
                $u->isdata = true;
                break;
                case 'issourcecode':
                $u->issourcecode = true;
                break;
                }

                R::store($u);
                $this ->redirect('/publication');
            }
            else
            {
                $context->local()->addval('message', 'You have not posted all of the required values.');
                return 'publication/create.twig';
            }
        }
        else
        {
            return 'publication/create.twig';
        }

    }

    public function handledelete($context, $id)
    {
        $bean = R::load('publication', intval($id));
        if ($_SERVER['REQUEST_METHOD']  == 'POST')
        {
            R::trash($bean);
            $this->redirect('/publication');
        }
        else
        {
            $context->local()->addval('publication', $bean);
            return 'publication/delete.twig';
        }
    }

    public function handleupdate($context, $id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if ( ($name = $context->postpar('name', '')) != '' &&
               ($description = $context->postpar('description', '')) != '' &&
               ($licence = $context->postpar('licence', '')) != '' &&
               ($authors = $context->postpar('authors', '')) != '' &&
               ($type = $context->postpar('type', '')) != '' &&
               ($data = $context->postpar('data', '')) != ''
             )
            {
                $u = R::load('publication', intval($id));
                $u->name = $name;
                $u->description = $description;
                $u->licence = $licence;
                $u->isdocument = false;
                $u->isapp = false;
                $u->isdata = false;
                $u->issourcecode = false;
                $u->data = $data;

                switch ($type)
                {
                case 'isdocument':
                $u->isdocument = true;
                break;
                case 'isapp':
                $u->isapp = true;
                break;
                case 'isdata':
                $u->isdata = true;
                break;
                case 'issourcecode':
                $u->issourcecode = true;
                break;
                }

                R::store($u);
                $this ->redirect('/publication');
            }
            else
            {
                $context->local()->addval('publication', R::load('publication', intval($id)));
                $context->local()->addval('message', 'You have not posted all of the required values.');
                return 'publication/update.twig';
            }
        }

        $context->local()->addval('publication', R::load('publication', intval($id)));
        return 'publication/update.twig';
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
        if (is_numeric($rest[0]))                //  /{0}/?
        {
            if (isset($rest[1]))                 //  /{0}/{string}
            {
                $ret['route'] = $rest[1];
                $ret['parameter'] = $rest[0];
            }
            else                                 //  /{0}
            {
                $ret['route'] = 'view';
                $ret['parameter'] = $rest[0];
            }
        }
        elseif ($rest[0] == '')                  //  /
        {
            $ret['route'] = 'index';
        }
        elseif ($rest[0] == 'type')              //  /type
        {
            if (!isset($rest[1]))                //  /type <-- error
            {
                throw new Exception('must provide a type');
            } else                               //  /type/{type}
            {
                $ret['route'] = 'type';
                $ret['parameter'] = $rest[1];
            }
        }
        else                                     //  /{other}
        {
            $ret['route'] = $rest[0];
        }
        return $ret;
    }

  }
?>
