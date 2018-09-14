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

require 'vendor/autoload.php';
$id = required_param('id', PARAM_INT);
$page = optional_param('page', 'start', PARAM_RAW);
$context = context_course::instance($id);
$parts = explode('_', $page);
$fs = get_file_storage();

\format_wiki\wiki_url::set_current_context($context);
\format_wiki\wiki_url::set_current_page($page);
$file = \format_wiki\wiki_url::from_moodle_url($page)->get_resource();
if ($file === false) {
    global $PAGE;
    $renderer = $PAGE->get_renderer('format_wiki');
    echo $renderer->render_from_template('format_wiki/pagenotexist', []);
} else {

    $markupConfig = new  Markup\Edux\Config($context, (new moodle_url('/course/view.php', ['id' => $id])) . '&page=%s');
    $generator = new \Generator\MoodleHtml\Document(new \Generator\MoodleHtml\Config());
    $wr = new \WikiRenderer\Renderer($generator, $markupConfig);

    echo '<div class="d-flex flex-row-reverse">';
    echo '<a class="btn btn-outline-primary ml-2" href="' .
            (new moodle_url('/course/format/wiki/revisions.php', ['id' => $id, 'page' => $page])) . '">' .
            get_string('revisions', 'format_wiki') . '</a>';
    echo '<a class="btn btn-outline-primary" href="' .
            (new moodle_url('/course/format/wiki/edit.php', ['id' => $id, 'page' => $page])) . '">' . get_string('edit') . '</a>';
    echo '</div>';
    // call render() method: it will parse DokuWiki syntax, and will
    // generate HTML content

    $course = course_get_format($course)->get_course();
    $renderer = $PAGE->get_renderer('format_wiki');
    $entity = \format_wiki\entity\format_wiki_section_mapping::create_or_get($course->id, $page);
    echo $renderer->print_wiki_page($wr->render($file->get_content()), $course, null, null, null, null, $entity->sectionid);
}