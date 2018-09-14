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

use local_cool\entity\course;
use local_cool\entity\database_entity;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

class format_wiki_section_mapping extends database_entity {
    public const TABLENAME = 'format_wiki_section_mapping';

    public $id;
    public $courseid;
    public $sectionid;
    public $page;

    public function get_file() : \stored_file {
        $fs = get_file_storage();
        $index = strrpos($this->page, '/');
        $folder = substr($this->page, 0, $index + 1);
        $file = substr($this->page, $index + 1) . '.txt';
        return $fs->get_file(\context_course::instance($this->courseid)->id, 'format_wiki', 'pages', 0
                , $folder, $file);
    }

    public static function create_or_get(int $course, string $page) : format_wiki_section_mapping {
        $entity = format_wiki_section_mapping::get(['courseid' => $course, 'page' => $page]);
        if ($entity != null) {
            return $entity;
        }
        $entity = new format_wiki_section_mapping();
        $entity->courseid = $course;
        $entity->page = $page;
        $section = course_create_section($course);
        $entity->sectionid = $section->id;
        $entity->save();
        return $entity;
    }

}