<?php

/**
 * DokuWiki syntax.
 *
 * @author Laurent Jouanneau
 * @copyright 2008-2016 Laurent Jouanneau
 *
 * @link http://wikirenderer.jelix.org
 *
 * @licence MIT see LICENCE file
 */

namespace Markup\Edux;

/**
 * Parser for emphasys inline tag.
 */
class Em extends \WikiRenderer\InlineTag {
    protected $name = 'em';
    protected $generatorName = 'em';
    protected $beginTag = '//';
    protected $endTag = '//';
}
