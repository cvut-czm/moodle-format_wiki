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

\format_wiki\wiki_url::set_current_context($context);
\format_wiki\wiki_url::set_current_page($page);

$pageurl = new moodle_url('/course/format/wiki/revisions.php', ['id' => $id, 'page' => $page]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_heading(get_string('title:revisions','format_wiki'));
$PAGE->set_title("{$SITE->shortname}");
$output = $PAGE->get_renderer('format_wiki');

echo $output->header();
$data = [];
$data['revisions'] = [];
$h = [];
$histories =
        \format_wiki\entity\format_wiki_history::get_all(['pageid' => \format_wiki\entity\format_wiki_section_mapping::get(['courseid' => $id,
                'page' => $page])->id]);
foreach ($histories as $history) {
    $h[] = [$history->timecreated, (int) $history->userid];
}
$h[] = [\local_cool\entity\course::get($id)->get_time_created(), 'external'];
$i = 0;
$data['revisions'][] = ['id' => 'current', 'date' => gmdate("Y-m-d H:i:s", $h[$i][0]),
        'user' => ((is_int($h[$i][1])) ? \local_cool\entity\user::get($h[$i][1])->get_username() : $h[$i][1])];
$i++;
foreach ($histories as $history) {
    $data['revisions'][] = //TODO: XSS protection
            [       'link_patchfile' => new moodle_url('/course/format/wiki/patchfile.php', ['id' => $history->id]),
                    'link_display'=> new moodle_url('/course/view.php',['id'=>$id,'page'=>$page]),
                    'link_rollback' => new moodle_url('/course/format/wiki/rollback.php',['id'=>$history->id]),
                    'link_compare' => new moodle_url('/course/format/wiki/diff_compare.php',['id'=>$history->id]),
                    'id' => $history->id,
                    'date' => gmdate("Y-m-d H:i:s", $h[$i][0]),
                    'user' => ((is_int($h[$i][1])) ? \local_cool\entity\user::get($h[$i][1])->get_username() : $h[$i][1])];
    $i++;
}
echo $output->render_from_template('format_wiki/revisions', $data);

echo $output->footer();