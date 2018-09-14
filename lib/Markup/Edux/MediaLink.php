<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is a one-line short description of the file.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    xxxxxx
 * @category   xxxxxx
 * @copyright  2018 CVUT CZM, Jiri Fryc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Markup\Edux;

use format_wiki\wiki_url;
use local_cool\crsbld\link_fixer;

defined('MOODLE_INTERNAL') || die();

class MediaLink extends \WikiRenderer\InlineTagWithSeparator {
    protected $name = 'a';
    protected $generatorName = 'link';
    protected $beginTag = '{{:';
    protected $endTag = '}}';
    protected $attribute = array('href', '$$');
    protected $separators = array('|');

    public function getContent() {
        $cntattr = count($this->attribute);
        $cnt = ($this->separatorCount + 1 > $cntattr) ? $cntattr : ($this->separatorCount + 1);

        $t=$this->wikiContentArr[0].$this->wikiContentArr[1];
        if(strpos($t,'tsort')>0 || strpos($t,'msort')>0 || strpos($t,'indexmenu')>0) {
            $this->generator=$this->documentGenerator->getInlineGenerator('hidden');
            return $this->generator;
        }
        $p=strpos($this->wikiContentArr[0],'?');
        if($p>0)
            $this->wikiContentArr[0]=substr($this->wikiContentArr[0],0,$p);
        $url=wiki_url::from_media_link($this->wikiContentArr[0]);
        list($href, $label) = $this->config->getLinkProcessor()->processMediaLink(':'.$this->wikiContentArr[0], $this->generatorName);
        $this->wikiContentArr[0] = $href;


        if ($cnt == 2) {
            ++$this->separatorCount;
            $this->wikiContentArr[1] = '';
            $this->generator->setRawContent($label);
        }
        $s=strpos($href,'?');
        $s=$s===false?0:$s;
        if (in_array(substr($href,strrpos($href,'.')+1,$s), ['jpg','png','svg','gif','bmp'])) {
            $this->generator = $this->documentGenerator->getInlineGenerator('image');
            $url=$url->get_resource();
            if($url!==false)
                $this->generator->setAttribute('src', (new link_fixer(wiki_url::get_current_context()))->read_image_to_base64($url));
            else
                $this->generator->setAttribute('src', $href);
            return $this->generator;
        }

        return parent::getContent();
    }
}