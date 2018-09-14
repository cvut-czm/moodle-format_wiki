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
use local_cool\crsbld\link_fixer;

/**
 * Configuration for the WikiRenderer parser for Dokuwiki markup.
 */
class Config extends \WikiRenderer\Config {
    public $defaultTextLineContainer = '\Markup\Edux\TextLine';
    public $textLineContainers = array(
            '\Markup\Edux\TextLine' => array(
                    '\Markup\Edux\ControlTag',
                    '\Markup\Edux\Strong',
                    '\Markup\Edux\Em',
                    '\Markup\Edux\Del',
                    '\Markup\Edux\Subscript',
                    '\Markup\Edux\Superscript',
                    '\Markup\Edux\Underline',
                    '\Markup\Edux\Code',
                    '\Markup\Edux\MediaLink',
                    '\Markup\Edux\Image',
                    '\Markup\Edux\Link',
                    '\Markup\Edux\NoWikiInline',
                    '\Markup\Edux\Footnote',
            ),
            '\Markup\Edux\TableRow' => array(
                    '\Markup\Edux\ControlTag',
                    '\Markup\Edux\Strong',
                    '\Markup\Edux\Em',
                    '\Markup\Edux\Del',
                    '\Markup\Edux\Subscript',
                    '\Markup\Edux\Superscript',
                    '\Markup\Edux\Underline',
                    '\Markup\Edux\Code',
                    '\Markup\Edux\MediaLink',
                    '\Markup\Edux\Image',
                    '\Markup\Edux\Link',
                    '\Markup\Edux\NoWikiInline',
                    '\Markup\Edux\Footnote',
            ),
    );
    /** List of block parsers. */
    public $blocktags = array(
            '\Markup\Edux\Note',
            '\Markup\Edux\Title',
            '\Markup\Edux\Comment',
            '\Markup\Edux\WikiList',
            '\Markup\Edux\Blockquote',
            '\Markup\Edux\Table',
            '\Markup\Edux\CodeBlock',
            '\Markup\Edux\File',
            '\Markup\Edux\NoWiki',
            '\Markup\Edux\Html',
            '\Markup\Edux\Php',
            '\Markup\Edux\Macro',
            '\Markup\Edux\Pre',
            '\Markup\Edux\P',
    );

    public $escapeChar = '';

    /**
     * top level header will be h1 if you set to 1, h2 if it is 2 etc..
     */
    public $startHeaderNumber = 1;

    /**
     * list of functions implementing macros. Functions receive the macro name and
     * a string representing the arguments. It should return a string (empty or not)
     * to insert into the generated content.
     *
     * @var callable[] keys are macro name in lower case
     */
    public $macros = array();

    public function __construct(\context_course $context, link_fixer $fixer) {
        $this->linkProcessor = new LinkProcessor($context, $fixer);
        $this->wordConverters[] = new \WikiRenderer\WordConverter\URLConverter($this->linkProcessor);
        $this->simpleTags[] = new LineBreak();
        $this->simpleTags[] = new \WikiRenderer\SimpleTag\Arrows();
        $this->simpleTags[] = new \WikiRenderer\SimpleTag\Trademark();
    }

    /**
     * Called before parsing.
     *
     * It should returns the given text. It may modify the text.
     *
     * @param string $text the wiki text
     *
     * @return string the wiki text
     */
    public function onStart($text) {
        return $text;
    }

    /**
     * Called after parsing.
     *
     * @param string $finalText the generated text in the target format (html...)
     *
     * @return string the final text, which may contains new modifications
     *                (content added at the begining or at the end for example)
     */
    public function onParse($finalText) {
        return $finalText;
    }
}
