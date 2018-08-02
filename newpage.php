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
$context = context_course::instance($id);

$pageurl = new moodle_url('/course/format/wiki/newpage.php', ['id' => $id]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_title("{$SITE->shortname}");
$PAGE->set_heading(get_string('title:newpage', 'format_wiki'));
$output = $PAGE->get_renderer('format_wiki');

$form = new \format_wiki\form\newpage_form($pageurl);
if($form->is_submitted() && $form->is_validated())
{
    $data=$form->get_data();
    $url=$data->pageurl;
    $url=str_replace('\\','/',$url);
    $url=strtolower($url);
    if($url[0]!='/')
        $url='/'.$url;
    if(strrpos($url,'/')==strlen($url)-1)
        $url.='start';
    $url.='.txt';

    $fs=get_file_storage();
    $path=substr($url,0,strrpos($url,'/')+1);
    $name=substr($url,strrpos($url,'/')+1);
    $fs->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'format_wiki',
            'filearea' => 'pages',
            'itemid' => 0,
            'filepath' => $path,
            'filename' => $name,
            'timecreated' => time(),
            'timemodified' => time()
    ], '======'.substr($name,0,-4).'======');

}

echo $output->header();
$form->display();
echo $output->footer();