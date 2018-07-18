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

namespace format_wiki;

defined('MOODLE_INTERNAL') || die();

class config {

    private static $ignored_folders=null;
    private static $ignored_pages=null;
    private static $teacher_folders=null;

    public static function get_ignored_folders() : array
    {
        global $CFG;
        if(self::$ignored_folders==null)
        {
            self::$ignored_folders=file($CFG->dirroot.'/course/format/wiki/cfg/ignored_folders.txt',FILE_IGNORE_NEW_LINES);
        }
        return self::$ignored_folders;
    }
    public static function get_ignored_pages() : array
    {
        global $CFG;
        if(self::$ignored_pages==null)
        {
            self::$ignored_pages=file($CFG->dirroot.'/course/format/wiki/cfg/ignored_pages.txt');
        }
        return self::$ignored_pages;
    }
    public static function get_teacher_folders() : array
    {
        global $CFG;
        if(self::$teacher_folders==null)
        {
            self::$teacher_folders=file($CFG->dirroot.'/course/format/wiki/cfg/teacher_folders.txt');
        }
        return self::$teacher_folders;
    }
}