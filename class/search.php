<?php

/**
 * A class that allows searching of documents
 *
 * @author Gavin Gregory <g.i.gregory@ncl.ac.uk>
 * @copyright 2015 Newcastle University
 *
 */

    class Search extends Siteaction
    {
        public function handle($context)
        {
            $util = new Utilities();
            $query = $context->getpar('q', '');
            $query = $util->sanitise($query);
            $query = '%'.$query.'%';
            $publications = R::getAll('SELECT * FROM publication WHERE name LIKE ? OR description LIKE ? OR authors LIKE ? OR tags LIKE ?', [$query, $query, $query, $query]);
            $context->local()->addval('publications', $publications);
            return 'publication/index.twig';
        }
    }

?>
