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

require_once('../../../config.php');
require_once('vendor/autoload.php');

$id = required_param('id',PARAM_INT);
$history=\format_wiki\entity\format_wiki_history::get($id);


$context=context_course::instance($history->get_page_entity()->courseid);
$pageurl = new moodle_url('/course/format/wiki/patchfile.php', ['id' => $id]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading(get_string('title:patchfile','format_wiki'));
$output = $PAGE->get_renderer('format_wiki');

echo $output->header();
echo $output->render_from_template('format_wiki/patchfile',['file'=>str_replace("\n","<br/>",$history->patch)]);
echo $output->footer();