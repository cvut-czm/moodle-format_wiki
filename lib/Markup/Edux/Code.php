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
 * Parse code inline tag.
 */
class Code extends \WikiRenderer\InlineTag
{
    protected $name = 'code';
    protected $generatorName = 'code';
    protected $beginTag = '\'\'';
    protected $endTag = '\'\'';

    public function isOtherTagAllowed()
    {
        return false;
    }
}
