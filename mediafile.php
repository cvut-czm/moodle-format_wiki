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

require('../../../config.php');

$args=explode('/',required_param('path',PARAM_RAW));
$id=required_param('id',PARAM_INT);
$context=context_course::instance($id);

require_login($id, true);

$filename = array_pop($args); // The last item in the $args array.
if (!$args) {
    $filepath = '/'; // $args is empty => the path is '/'
} else {
    $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
}

// Retrieve the file from the Files API.
$fs = get_file_storage();
$file = $fs->get_file($context->id, 'format_wiki', 'media', 0, $filepath, $filename);
if (!$file) {
    return false; // The file does not exist.
}

// We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
// From Moodle 2.3, use send_stored_file instead.
send_file($file, $filename, 0, false, []);