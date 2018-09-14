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
use Generator\MoodleHtml\NoteLi;
use Generator\MoodleHtml\NoteUl;

/**
 * Parser for nowiki content.
 */
class Note extends \WikiRenderer\Block {
    public $type = 'note';
    protected $tagName = 'note';

    protected $closeTagDetected = false;

    protected $_args = null;

    public function isStarting($string) {
        if (preg_match('/^\s*<' . $this->tagName . '(?:\s([^>]+))?>(.*)/i', $string, $m)) {
            $this->_args = $m;
            if (preg_match('/(.*)<\/' . $this->tagName . '>\s*$/i', $m[2], $m2)) {
                $this->_closeNow = true;
                $this->_detectMatch = $m2[1];
                $this->closeTagDetected = true;
            } else {
                $this->_closeNow = false;
                $this->_detectMatch = $m[2];
            }
            $this->generator->class=$m[1];

            return true;
        } else {
            return false;
        }
    }

    public function open() {
        $this->closeTagDetected = false;
        parent::open();
    }
    private $list_cur=0;
    private $open=0;
    public function validateLine() {
        if (!$this->closeTagDetected || $this->_detectMatch != '') {
            if(preg_match('/^(\s+)\*\s(.*)$/',$this->_detectMatch,$m))
            {
                $l=strlen($m[1]);
                if($l>$this->list_cur)
                {
                    $this->generator->addLine(new NoteUl($this->documentGenerator->getConfig()));
                    $this->list_cur=$l;
                    $this->open++;
                }
                else if($l<$this->list_cur)
                {
                    $ul=new NoteUl($this->documentGenerator->getConfig());
                    $ul->setClosing(true);
                    $this->generator->addLine($ul);
                    $this->list_cur=$l;
                    $this->open--;
                    $li=new NoteLi($this->documentGenerator->getConfig());
                    $li->setClosing(true);
                    $this->generator->addLine($li);
                }

                $this->generator->addLine(new NoteLi($this->documentGenerator->getConfig()));
                $this->generator->addLine($this->parseInlineContent($m[2]));
                return;
            }
            else {
                while ($this->open > 0) {
                    $ul = new NoteUl($this->documentGenerator->getConfig());
                    $ul->setClosing(true);
                    $this->generator->addLine($ul);
                    $this->open--;
                    if($this->open>1)
                    {
                        $li=new NoteLi($this->documentGenerator->getConfig());
                        $li->setClosing(true);
                        $this->generator->addLine($li);
                    }
                }
                $this->generator->addLine($this->parseInlineContent($this->_detectMatch));
            }
        }
        else {
            while ($this->open > 0) {
                $ul = new NoteUl($this->documentGenerator->getConfig());
                $ul->setClosing(true);
                $this->generator->addLine($ul);
                $this->open--;
                if($this->open>1)
                {
                    $li=new NoteLi($this->documentGenerator->getConfig());
                    $li->setClosing(true);
                    $this->generator->addLine($li);
                }
            }
        }
    }

    public function isAccepting($string) {
        if ($this->closeTagDetected) {
            return false;
        }

        $this->_args = null;
        if (preg_match('/(.*)<\/' . $this->tagName . '>\s*$/i', $string, $m)) {
            $this->_detectMatch = $m[1];
            $this->closeTagDetected = true;
        } else {
            $this->_detectMatch = $string;
        }

        return true;
    }
}
