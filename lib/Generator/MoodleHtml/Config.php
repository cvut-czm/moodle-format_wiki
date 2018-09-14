<?php

/**
 * Configuration for an HTML generator.
 *
 * @author Laurent Jouanneau
 * @copyright 2016 Laurent Jouanneau
 *
 * @link http://wikirenderer.jelix.org
 *
 * @licence MIT see LICENCE file
 */

namespace Generator\MoodleHtml;

/**
 * Base class for the configuration.
 */
class Config extends \WikiRenderer\Generator\Config {
    public $htmlEncloseContent = true;

    public $inlineGenerators = array(
            'textline' => '\Generator\MoodleHtml\TextLine',
            'words' => '\Generator\MoodleHtml\Words',
            'strong' => '\Generator\MoodleHtml\Strong',
            'em' => '\Generator\MoodleHtml\Em',
            'del' => '\Generator\MoodleHtml\Del',
            'subscript' => '\Generator\MoodleHtml\Sub',
            'superscript' => '\Generator\MoodleHtml\Sup',
            'variable' => '\Generator\MoodleHtml\Variable',
            'key' => '\Generator\MoodleHtml\Key',
            'insert' => '\Generator\MoodleHtml\Ins',
            'underline' => '\Generator\MoodleHtml\Underline',
            'code' => '\Generator\MoodleHtml\Code',
            'hidden' => '\Generator\MoodleHtml\Hidden',
            'quote' => '\Generator\MoodleHtml\Quote',
            'cite' => '\Generator\MoodleHtml\Cite',
            'acronym' => '\Generator\MoodleHtml\Acronym',
            'link' => '\Generator\MoodleHtml\Link',
            'image' => '\Generator\MoodleHtml\Image',
            'video' => '\Generator\MoodleHtml\Video',
            'audio' => '\Generator\MoodleHtml\Audio',
            'flash' => '\Generator\MoodleHtml\Flash',
            'anchor' => '\Generator\MoodleHtml\Anchor',
            'linebreak' => '\Generator\MoodleHtml\LineBreak',
            'tablecell' => '\Generator\MoodleHtml\TableCell',
            'noformat' => '\Generator\MoodleHtml\NoFormat',
            'footnotelink' => '\Generator\MoodleHtml\FootnoteLink',
    );

    public $blockGenerators = array(
            'title' => '\Generator\MoodleHtml\Title',
            'list' => '\Generator\MoodleHtml\HtmlList',
            'noformat' => '\Generator\MoodleHtml\NoFormatBlock',
            'none' => '\Generator\MoodleHtml\None',
            'note' => '\Generator\MoodleHtml\Note',
            'pre' => '\Generator\MoodleHtml\Preformated',
            'syntaxhighlight' => '\Generator\MoodleHtml\SyntaxHighlighting',
            'blockquote' => '\Generator\MoodleHtml\BlockQuote',
            'hr' => '\Generator\MoodleHtml\Hr',
            'para' => '\Generator\MoodleHtml\Paragraph',
            'definition' => '\Generator\MoodleHtml\Definition',
            'table' => '\Generator\MoodleHtml\Table',
            'html' => '\Generator\MoodleHtml\Html',
            'footnotes' => '\Generator\MoodleHtml\Footnotes',

    );
}
