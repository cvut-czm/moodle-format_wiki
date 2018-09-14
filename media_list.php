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

$id = required_param('id', PARAM_INT);

$context = context_course::instance($id);

require_login();
require_capability('local/kos:load_course_data',$context);


$pageurl = new moodle_url('/course/format/wiki/media_list.php', ['id' => $id]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading(get_string('title:medialist', 'format_wiki'));
$output = $PAGE->get_renderer('format_wiki');

$fs=get_file_storage();
$files=$fs->get_area_files($context->id,'format_wiki','media',0,"itemid, filepath, filename",false);
$data = ['files'=>[]]; $i=-1;
function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}
foreach ($files as $file)
{
    if($data['files'][$i]['key']!==$file->get_filepath()) {
        $i++;
        $data['files'][] = ['key' => $file->get_filepath(), 'value' => []];
        $last=$file->get_filepath();
    }
    $data['files'][$i]['value'][]=[
            'name'=>$file->get_filename(),
            'size'=>human_filesize($file->get_filesize()),
            'modified'=>date('j.n.Y G:i:s', $file->get_timemodified()),
            'created'=>date('j.n.Y G:i:s', $file->get_timecreated()),
            'mimetype'=>$file->get_mimetype(),
            'download'=>((string)new moodle_url('/course/format/wiki/mediafile.php')).'?id='.$id.'&path='.$file->get_filepath().$file->get_filename(),
            'edit'=>((string)new moodle_url('/course/format/wiki/mediafileedit.php')).'?id='.$id.'&path='.$file->get_filepath().$file->get_filename(),
            'delete'=>((string)new moodle_url('/course/format/wiki/mediafiledelete.php')).'?id='.$id.'&path='.$file->get_filepath().$file->get_filename()
    ];
}
echo $output->header();
echo $output->render_from_template('format_wiki/media_list', $data);
echo $output->footer();