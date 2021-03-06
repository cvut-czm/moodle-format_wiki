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
 * Parser for code content.
 */
class CodeBlock extends NoWiki {
    public $type = 'syntaxhighlight';
    protected $tagName = 'code';

    public function validateLine() {
        if ($this->_args && $this->_args[1] != '') {
            $args = preg_split("/\s+/", $this->_args[1], 2);
            $this->generator->setSyntaxType($args[0]);
        }
        if (!$this->closeTagDetected || $this->_detectMatch != '') {
            $this->generator->addLine($this->_detectMatch);
        }
    }
}
