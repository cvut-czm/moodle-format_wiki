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
 * Parser for preformated content.
 */
class Pre extends \WikiRenderer\Block {
    public $type = 'pre';
    protected $regexp = "/^(\s{2,})(.*)/";

    public function validateLine() {
        $this->generator->addLine($this->_detectMatch[1] . $this->_detectMatch[2]);
    }
}
