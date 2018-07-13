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
$page = optional_param('page', '', PARAM_ALPHANUMEXT);
$context = context_course::instance($id);
$parts = explode('_', $page);
$fs = get_file_storage();

function parse($parts, $context): stored_file {
    $fs = get_file_storage();
    $tree = $fs->get_area_tree($context->id, 'format_wiki', 'wiki', 0);
    $tree = $tree['subdirs']['pages'];
    $c = 0;
    $count = count($parts);
    $preamble = '';
    foreach ($parts as $part) {
        if (!empty($preamble)) {
            $part = $preamble . '_' . $part;
        }
        if (empty($part)) {
            $c++;
            continue;
        } else if (isset($tree['subdirs'][$part])) {
            $tree = $tree['subdirs'][$part];
            $c++;
            continue;
        } else if ($c + 1 == $count) {
            if (isset($tree['files'][$part . '.txt'])) {
                return $tree['files'][$part . '.txt'];
            } else if (isset($tree['files']['start.txt'])) {
                return $tree['files']['start.txt'];
            }
        }
        $preamble = $part;
        $c++;
    }
}

$file = parse($parts, $context);
$markupConfig = new  Markup\Edux\Config($context, (new moodle_url('/course/view.php', ['id' => $id])) . '&page=%s');

// then choose a generator, e.g., the object which generates
// the result text in the expected format. Here, HTML...
$genConfig = new \Generator\MoodleHtml\Config();

$generator = new \Generator\MoodleHtml\Document($genConfig);

// now instancy the WikiRenderer engine
$wr = new \WikiRenderer\Renderer($generator, $markupConfig);

echo '<div class="d-flex flex-row-reverse">';
echo '<a class="btn btn-outline-primary ml-2" href="#">Revize</a>';
echo '<a class="btn btn-outline-primary" href="#">Editovat stránku</a>';
echo '</div>';
// call render() method: it will parse DokuWiki syntax, and will
// generate HTML content
echo $wr->render($file->get_content());