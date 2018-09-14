<?php

/**
 * DokuWiki syntax.
 *
 * @author Laurent Jouanneau
 * @copyright 2008-2018 Laurent Jouanneau
 *
 * @link http://wikirenderer.jelix.org
 *
 * @licence MIT see LICENCE file
 */

namespace Markup\Edux;
use format_wiki\wiki_url;
use local_cool\crsbld\link_fixer;

/**
 * Parser for an image inline tag.
 */
class Image extends \WikiRenderer\InlineTagWithSeparator {
    protected $name = 'image';
    protected $generatorName = 'image';
    protected $beginTag = '{{';
    protected $endTag = '}}';
    protected $attribute = array('filesrc', 'title');
    protected $separators = array('|');

    public function getContent() {
        $contents = $this->wikiContentArr;
        if (count($contents) == 1) {
            $href = $contents[0];
            $title = '';
        } else {
            $href = $contents[0];
            $title = $contents[1];
        }
        $t=$href.$title;
        if(strpos($t,'tsort')>0 || strpos($t,'msort')>0 || strpos($t,'indexmenu')>0) {
            $this->generator=$this->documentGenerator->getInlineGenerator('hidden');
            return $this->generator;
        }

        $align = '';
        $width = '';
        $height = '';
        $linkonly = false;

        $m = array('', '', '', '', '', '', '', '');
        if (preg_match("/^(\s*)([^\s\?]+)(\?[a-zA-Z0-9]+)?(\s*)$/", $href, $m)) {
            if ($m[1] != '' && $m[4] != '') {
                $align = 'center';
            } else if ($m[1] != '') {
                $align = 'right';
            } else if ($m[4] != '') {
                $align = 'left';
            }
            $href = $m[2];
            if ($m[3]) {
                if (preg_match("/^\?(\d+)(x(\d+))?$/", $m[3], $m2)) {
                    $width = $height = $m2[1];
                    if (isset($m2[2])) {
                        $height = $m2[3];
                    }
                } else if ($m[3] == '?linkonly') {
                    $linkonly = true;
                }
            }
        }
        $g=$href;
        list($href, $label) = $this->config->getLinkProcessor()->processMediaLink($href, $this->name);
        if ($linkonly) {
            $this->generator = $this->documentGenerator->getInlineGenerator('link');
            $this->generator->setAttribute('href', $href);
            $this->generator->setRawContent(($title ?: $label));

            return $this->generator;
        }

        $type = 0;
        if (preg_match('/\.([a-zA-Z0-9]+)$/', $href, $m)) {
            $ext = $m[1];
            switch ($ext) {
                case 'mp4' :
                case 'webm':
                case 'ogv':
                    $this->generator = $this->documentGenerator->getInlineGenerator('video');
                    $type = 1;
                    break;
                case 'mp3':
                case 'ogg':
                case 'wav':
                    $this->generator = $this->documentGenerator->getInlineGenerator('audio');
                    $type = 2;
                    break;
                case 'swf':
                    $this->generator = $this->documentGenerator->getInlineGenerator('flash');
                    $type = 3;
                    break;
                case 'jpg':
                case 'png':
                case 'bmp':
                case 'gif':
                    $wiki = wiki_url::from_media_link($g);
                    $res=$wiki->get_resource();
                    if($res===false)
                        break;
                    $this->generator = $this->documentGenerator->getInlineGenerator('image');
                    $this->generator->setAttribute('src', (new link_fixer(wiki_url::get_current_context()))->read_image_to_base64($res));
                    return $this->generator;
                    break;
                default:
                    $this->generator = $this->documentGenerator->getInlineGenerator('link');
                    $this->generator->setAttribute('href', $href);
                    $this->generator->setRawContent(($title ?: $label));

                    return $this->generator;
            }
        }

        $this->generator->setAttribute('src', $href);
        if ($type != 2) {
            if ($width != '') {
                $this->generator->setAttribute('width', $width);
            }
            if ($height != '') {
                $this->generator->setAttribute('height', $height);
            }
        }
        if ($align != '') {
            $this->generator->setAttribute('align', $align);
        }
        if ($title != '') {
            if ($type == 0) {
                $this->generator->setAttribute('alt', $title);
            } else {
                $this->generator->setAttribute('title', $title);
            }
        }

        return $this->generator;
    }
}
