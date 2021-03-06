<?php

/**
 * DokuWiki syntax.
 *
 * @author Laurent Jouanneau
 * @copyright 2006-2016 Laurent Jouanneau
 *
 * @link http://wikirenderer.jelix.org
 *
 * @licence MIT see LICENCE file
 */

namespace Markup\Edux;

/**
 * Parser for underline inline tag.
 */
class Underline extends \WikiRenderer\InlineTag {
    protected $name = 'u';
    protected $generatorName = 'underline';
    protected $beginTag = '__';
    protected $endTag = '__';
}
