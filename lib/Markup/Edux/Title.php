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
 * Parse a title block.
 */
class Title extends \WikiRenderer\Block {
    public $type = 'title';
    protected $regexp = "/^\s*(\=+)\s*([^=]+)\s*(\=+)\s*$/";
    protected $_closeNow = true;

    public static $last_title = null;

    public function validateLine() {
        $level = strlen($this->_detectMatch[1]);
        $h = 6 - $level + $this->engine->getConfig()->startHeaderNumber;
        if ($h > 6) {
            $h = 6;
        } else if ($h < 1) {
            $h = 1;
        }
        $this->generator->setLevel($h);
        $title = trim($this->_detectMatch[2]);
        if ($h == 1 && self::$last_title === null) {
            self::$last_title = $title;
        } else {
            $this->generator->addLine($this->parseInlineContent($title));
        }
    }
}
