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
            $site = new SiteInfo();
            $errors = array();
            if ($_SERVER['REQUEST_METHOD']  == 'POST')
            { //POST
                if (
                   $site->test_form_input(($name = $context->mustpostpar('name', ''))) != '' &&
                   $site->test_form_input(($description = $context->mustpostpar('description', ''))) != '' &&
                   $site->test_form_input(($licence = $context->mustpostpar('licence', ''))) != '' &&
                   $site->test_form_input(($authors = $context->mustpostpar('authors', ''))) != '' &&
                   $site->test_form_input(($type = $context->mustpostpar('type', ''))) != '' &&
                   $site->test_form_input(($tags = $context->mustpostpar('tags', ''))) != '' &&
                   $site->test_form_input(($data = $context->mustpostpar('data', ''))) != ''
                 )
                { // initial validation successful

                    // handle file upload
                    if (empty($_FILES))
                    {
                        throw new Exception('empty file');
                    }

                    $targetfile = SiteInfo::$uploadsdir . basename($_FILES['file']['name']);
                    $filetype = pathinfo($targetfile, PATHINFO_EXTENSION);

                    // check if the file exists
                    if (file_exists($targetfile))
                    {
                        array_push($errors, 'Target file exists.');
                    }

                    // check if the file is greater than the upload limit size
                    if ($_FILES['file']['size'] > SiteInfo::$uploadslimit)
                    {
                        array_push($errors, 'File size is too large. The maximum is '.SiteInfo::$uploadslimit.'kb');
                    }

                    // check if the filetype is allowed
                    $typematch = false;
                    foreach (SiteInfo::$uploadsfiletypes as $type)
                    {
                        if ($filetype == $type) $typematch = true;
                    }
                    if (!$typematch)
                    {
                        array_push($errors, 'File type is not supported.');
                    }

                    if (!empty($errors))
                    {
                        $context->local()->addval('errors', $errors);
                        return 'publication/create.twig';
                    }

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
                    if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetfile))
                    {
                        throw new Exception('File upload error.');
                    }
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
                $site = new SiteInfo();
                if (
                   $site->test_form_input(($name = $context->postpar('name', ''))) != '' &&
                   $site->test_form_input(($description = $context->postpar('description', ''))) != '' &&
                   $site->test_form_input(($licence = $context->postpar('licence', ''))) != '' &&
                   $site->test_form_input(($authors = $context->postpar('authors', ''))) != '' &&
                   $site->test_form_input(($type = $context->postpar('type', ''))) != '' &&
                   $site->test_form_input(($data = $context->postpar('data', ''))) != ''
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
