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
 * Handle the profile route. This function calls getaction() to extract the
 * 'route' and 'parameter' from the url. It then calls the correct
 * route function -> ie the correct controller.
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

/**
 * Index controller
 *
 * @param object	$context	The context object for the site
 *
 * @return string	The path of the publication list template
 */
        public function handleindex($context)
        {
            $context->local()->addval('publications', R::find('publication'));
            return 'publication/index.twig';
        }

/**
 * View controller
 *
 * @param object	$context	The context object for the site
 * @param integer	$id	The id of the publication to view
 *
 * @return string	The path of the publication view template
 */
        public function handleview($context, $id)
        {
            $context->local()->addval('publication', R::load('publication', intval($id)));
            return 'publication/view.twig';
        }

/**
 * Type controller retrieves only the publications of the correct type
 *
 * @param object	$context	The context object for the site
 * @param integer	$type	The type of the publication to view
 *
 * @return string	The path of the publication view template
 */
        public function handletype($context, $type)
        {
            if ($type == 'document' || $type == 'data' || $type == 'sourcecode' || 'app')
            { // checking $type protects against sql injection attacks
                $context->local()->addval('publications', R::find('publication', 'is'.$type.'=1'));
                return 'publication/index.twig';
            }
            else
            {
                throw new Exception('unhandled type.');
            }
        }

/**
 * Create controller
 * Handles GET and POST methods
 *
 * @param object	$context	The context object for the site
 *
 * @return string	The path of the publication create template
 */
        public function handlecreate($context)
        {
            $context->mustbeuser(); // MUST be a user! eh!
            $util = new Utilities();
            if ($_SERVER['REQUEST_METHOD']  == 'POST')
            { //POST
                if (
                   $util->sanitise(($name = $context->mustpostpar('name', ''))) != '' &&
                   $util->sanitise(($description = $context->mustpostpar('description', ''))) != '' &&
                   $util->sanitise(($licence = $context->mustpostpar('licence', ''))) != '' &&
                   $util->sanitise(($authors = $context->mustpostpar('authors', ''))) != '' &&
                   $util->sanitise(($type = $context->mustpostpar('type', ''))) != '' &&
                   $util->sanitise(($tags = $context->mustpostpar('tags', ''))) != '' &&
                   $util->sanitise(($data = $context->mustpostpar('data', ''))) != ''
                 )
                { // initial validation successful

                    // check file for errors
                    $errors = FileHandler::checkfile($_FILES['file']);
                    if (!empty($errors))
                    {
                        $context->local()->addval('errors', $errors);
                        return 'publication/create.twig';
                    }

                    // create and save bean
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

                    // store file
                    FileHandler::uploadfile($_FILES['file'], $u->id());

                    // TODO: perhaps delete the bean at this stage?

                    $this ->redirect('/publication');
                }
                else
                { // validation unsuccessful
                    array_push($errors, 'You have not posted all of the required values.');
                    $context->local()->addval('errors', $errors);
                    return 'publication/create.twig';
                }
            }
            else
            { //GET
                return 'publication/create.twig';
            }

        }

/**
 * Delete controller handles the deletion of publications
 * Handles GET and POST methods
 *
 * @param object	$context	The context object for the site
 * @param integer	$id	The id of the publication to delete
 *
 * @return string	The path of the publication delete template
 */
        public function handledelete($context, $id)
        {
            $context->mustbeuser();
            // TODO: must be owner
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

/**
 * Update controller handles the update of publications
 * Handles GET and POST methods
 *
 * @param object	$context	The context object for the site
 * @param integer	$id	The id of the publication to update
 *
 * @return string	The path of the publication update template
 */

        public function handleupdate($context, $id)
        {
            $context->mustbeuser();
            //TODO: must be owner
            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $util = new Utilities();
                if (
                   $util->sanitise(($name = $context->postpar('name', ''))) != '' &&
                   $util->sanitise(($description = $context->postpar('description', ''))) != '' &&
                   $util->sanitise(($licence = $context->postpar('licence', ''))) != '' &&
                   $util->sanitise(($authors = $context->postpar('authors', ''))) != '' &&
                   $util->sanitise(($type = $context->postpar('type', ''))) != '' &&
                   $util->sanitise(($data = $context->postpar('data', ''))) != ''
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
                    $this ->redirect('/publication/'.$u->id);
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


/**
 * Redirect handles redirection to a URL
 *
 * @param object	$url	The url to redirect to
 *
 * @return integer	Always returns 0
 */

        public function redirect($url)
        {
            header('Location: ' . $url, true, 302);
            return 0;
        }

/**
 * getaction generates an object with a 'route' property and a 'parameter'
 * property. As the REST api has different formats in different orders, it
 * abstracts away the complexity into a nice neat little object.
 *
 * @param array	$rest	The path tokens in an array
 *
 * @return object	Object with 'route' and possible 'parameter' properties set
 */

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
