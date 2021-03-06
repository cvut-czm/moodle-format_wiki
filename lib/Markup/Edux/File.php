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
 * Parser for file content.
 */
class File extends NoWiki {
    public $type = 'syntaxhighlight';
    protected $tagName = 'file';

    public function validateLine() {
        if ($this->_args && $this->_args[1] != '') {
            $args = preg_split("/\s+/", $this->_args[1], 2);
            if ($args[0] != '-') {
                $this->generator->setSyntaxType($args[0]);
            }
            if (count($args) > 1) {
                $this->generator->setFileName($args[1]);
            }
        }
        if (!$this->closeTagDetected || $this->_detectMatch != '') {
            $this->generator->addLine($this->_detectMatch);
        }
    }
}
