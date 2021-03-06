<?php

/**
 * @author Laurent Jouanneau
 * @copyright 2016 Laurent Jouanneau
 *
 * @link http://wikirenderer.jelix.org
 *
 * @licence MIT see LICENCE file
 */

namespace Generator\MoodleHtml;

class Cite extends AbstractInlineGenerator {
    protected $htmlTagName = 'cite';

    protected $supportedAttributes = array('id', 'title');
}
