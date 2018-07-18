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

namespace format_wiki\entity;

use DiffMatchPatch\DiffMatchPatch;
use local_cool\entity\database_entity;

defined('MOODLE_INTERNAL') || die();

class format_wiki_history extends database_entity {
    public const TABLENAME = 'format_wiki_history';

    public $id;
    public $patch;
    public $userid;
    public $timecreated;
    public $pageid;

    public function get_content() {
        $histories = self::get_all(['pageid' => $this->pageid]);
        $file = $this->get_page_entity()->get_file()->get_content();
        $dms = new DiffMatchPatch();
        usort($histories, function(format_wiki_history $a, format_wiki_history $b) {
            if ($a->timecreated == $b->timecreated) {
                return 0;
            }
            return $a->timecreated > $b->timecreated ? -1 : 1;
        });
        foreach($histories as $h)
        {
            $file=$dms->patch_apply($dms->patch_fromText($h->patch),$file)[0];
            if($h->id===$this->id)
                break;
        }
        return $file;
    }

    public function get_page_entity(): format_wiki_section_mapping {
        return format_wiki_section_mapping::get($this->pageid);
    }

}