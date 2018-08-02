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
$page = optional_param('page', 'start', PARAM_RAW);
$context = context_course::instance($id);
require_login();
require_capability('moodle/course:changesummary', $context);

\format_wiki\wiki_url::set_current_context($context);
\format_wiki\wiki_url::set_current_page($page);
$file = \format_wiki\wiki_url::from_moodle_url('/' . $page)->get_resource();

if (isset($_POST['wikipage'])) {
    $wikipage = $_POST['wikipage'];
    $wikipage = str_replace("\r\n", "\n", $wikipage);
    if ($wikipage == '') {
        if ($file !== false) {
            $file->delete();
        }
    } else {
        if ($file !== false) {
            $fs = $file->get_content();
        } else {
            $fs = '';
        }
        if (\format_wiki\revisions::is_changed($wikipage, $fs)) {
            global $USER;
            $patch = \format_wiki\revisions::get_patch($wikipage, $fs);
            $history = new \format_wiki\entity\format_wiki_history();
            $history->patch = $patch;
            $section_mapping = \format_wiki\entity\format_wiki_section_mapping::create_or_get($id, $page);
            $history->pageid = $section_mapping->id;
            $history->timecreated = time();
            $history->userid = $USER->id;
            $history->save();
            if ($file !== false) {
                $name = $file->get_filename();
                $path = $file->get_filepath();
                $file->delete();
            } else {
                $parts = explode('/', $page);
                $name = array_pop($parts) . '.txt'; // The last item in the $args array.
                if (!$parts) {
                    $path = '/'; // $args is empty => the path is '/'
                } else {
                    $path = implode('/', $parts) . '/'; // $args contains elements of the filepath
                }
            }
            $fs = get_file_storage();
            if (strlen($wikipage) == 0) {
                $file = $fs->get_file($context->id, 'format_wiki', 'pages', 0, $path, $name);
                if ($file) {
                    $file->delete();
                }
                redirect(new moodle_url('/course/view.php', ['id' => $id]));die();
            } else {
                $fs->create_file_from_string(
                        [
                                'contextid' => $context->id,
                                'component' => 'format_wiki',
                                'filearea' => 'pages',
                                'itemid' => 0,
                                'filepath' => $path,
                                'filename' => $name,
                                'timecreated' => time(),
                                'timemodified' => time()
                        ],
                        $wikipage
                );
            }
        }
        redirect(new moodle_url('/course/view.php', ['id' => $id, 'page' => $page]));die();
    }
}

$pageurl = new moodle_url('/course/format/wiki/edit.php', ['id' => $id, 'page' => $page]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading(get_string('title:edit', 'format_wiki'));
$output = $PAGE->get_renderer('format_wiki');

echo $output->header();
$data = $file === false ? '' : $file->get_content();
$wysiwyg = \format_wiki\wysiwyg::create_default('wikipage', $data);
echo $output->render_from_template('format_wiki/edit',
        ['page' => $wysiwyg->render(), 'page_url' => $page, 'id' => $id, 'url' => $pageurl]);

echo $output->footer();