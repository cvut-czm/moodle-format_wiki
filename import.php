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

$id = required_param('id', PARAM_INT);

$PAGE->set_pagelayout('admin');
$url = new moodle_url('/course/format/wiki/import.php', ['id' => $id]);
$PAGE->set_url($url);
require_login();

$editform = new \format_wiki\form\import_form($url);
if ($editform->is_submitted() && $editform->is_validated()) {
    $context = context_course::instance($id);
    $data = $editform->get_submitted_data();
    $porter = new \format_wiki\porter();
    $porter->set_context($context);
    $porter->set_pages($editform->save_temp_file('pagefile'));
    $porter->set_media($editform->save_temp_file('mediafile'));
    if (isset($data->deleteoldwiki)) {
        $porter->delete_old();
    }
    $porter->port(isset($data->overwrite))->cleanup();
}

$PAGE->set_title('Import');
$PAGE->set_heading('Import from EDUX');

echo $OUTPUT->header();

$editform->display();

echo $OUTPUT->footer();