<?php

/**
 * @author Laurent Jouanneau
 * @copyright 2016 Laurent Jouanneau
 *
 * @link http://wikirenderer.jelix.org
 *
 * @licence MIT see LICENCE file
 */

namespace Markup\Edux;
use format_wiki\wiki_url;

/**
 * link processor that support Trac url.
 */
class LinkProcessor implements \WikiRenderer\LinkProcessor\LinkProcessorInterface {
    /**
     * the base root url from which resources other than
     * wiki page can be found.
     */
    public $appBaseUrl = '/';

    /**
     * base url of wiki pages.
     */
    public $wikiBaseUrl = '/wiki/%s';

    public $interwikiLinks = array(
            'wp' => 'http://wikipedia.org/%s',
    );

    public $context;

    public function __construct(\context_course $context, $wikiBaseUrl = '', $appBaseUrl = '/') {
        $this->wikiBaseUrl = $wikiBaseUrl ?: '/wiki/%s';
        $this->appBaseUrl = $appBaseUrl;
        $this->context = $context;
    }

    public function processLink($url, $tagName = '') {
        $label = $url;

        if (preg_match('/^(\w+)>(.*)$/', $url, $m)) {
            // interwiki links
            $anchor = '';
            if (preg_match('/(#[\w\-_\.0-9]+)$/', $m[1], $m2)) {
                $anchor = $m2[1];
                $m[2] = substr($m[1], 0, -strlen($m2[1]));
            }

            $label = $m[2];
            if ($m[1] == 'this') {
                $url = $this->appBaseUrl . $m[2] . $anchor;
            } else if (isset($this->interwikiLinks[$m[1]])) {
                $url = sprintf($this->interwikiLinks[$m[1]], $m[2]) . $anchor;
            } else {
                $url = sprintf($this->wikiBaseUrl, $m[2]) . $anchor;
            }
        } else if (!preg_match('!^[a-zA-Z]+\://!', $url)) {
            // wiki pages
            if (strpos($url, 'javascript:') !== false) { // for security reason
                $url = '#';
                $label = '#';
            } else if (preg_match('/(#[\w\-_\.0-9]+)$/', $url, $m)) {
                if ($url[0] == '#') {
                    $label = $url;
                } else {
                    $label = $url = substr($url, 0, -strlen($m[1]));
                    $url = sprintf($this->wikiBaseUrl, str_replace(':', '_', $url)) . $m[1];
                }
            } else {
                $label=wiki_url::from_wiki_link($label)->get_page();
                $url = sprintf($this->wikiBaseUrl, $label);
            }
        }

        if (strlen($label) > 40) {
            $label = substr($label, 0, 40) . '(..)';
        }

        return [$url, $label];
    }

    public function processMediaLink($a, $b) {
        $fs = get_file_storage();
        $wiki=wiki_url::from_media_link($a);
        $wikiurl=$wiki->get_media_url()->raw_out(false);
        return [$wikiurl, substr($wiki->get_page(),strrpos($wiki->get_page(),'/')+1)];
    }
}
