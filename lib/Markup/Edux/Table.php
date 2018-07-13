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
 * Parse a table block.
 */
class Table extends \WikiRenderer\Block
{
    public $type = 'table';
    protected $regexp = "/^\s*(\||\^)(.*)/";
    protected $_colcount = 0;

    public function open()
    {
        $this->_colcount = 0;
        $this->engine->getConfig()->defaultTextLineContainer = '\Markup\Edux\TableRow';
        parent::open();
    }

    public function close($reason)
    {
        $this->engine->getConfig()->defaultTextLineContainer = '\Markup\Edux\TextLine';

        return parent::close($reason);
    }

    public function validateLine()
    {
        $this->generator->createRow();
        // $generator is supposed to be a InlineBagGenerator class
        $generator = $this->parseInlineContent($this->_detectMatch[2]);

        $cells = $generator->getGenerators();
        foreach ($cells as $k => $generator) {
            if ($k === 0 && $this->_detectMatch[1] ==  '^') {
                $generator->setIsHeader(true);
            }

            $this->generator->addCell($generator);
        }
    }
}
