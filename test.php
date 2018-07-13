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
require 'vendor/autoload.php';
require_login();
$pageurl = new moodle_url('/local/kos/addcourse.php');
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$markupConfig = new \WikiRenderer\Markup\DokuWiki\Config();

// then choose a generator, e.g., the object which generates
// the result text in the expected format. Here, HTML...
$genConfig = new \WikiRenderer\Generator\Html\Config();
$generator = new \WikiRenderer\Generator\Html\Document($genConfig);

// now instancy the WikiRenderer engine
$wr = new \WikiRenderer\Renderer($generator, $markupConfig);

// call render() method: it will parse DokuWiki syntax, and will
// generate HTML content
$html = $wr->render('');

$fs = get_file_storage();

$id = optional_param('id', 20, PARAM_INT);
$context = context_course::instance($id);
$files = $fs->get_area_tree($context->id, 'format_wiki', 'wiki', 0);
$files = $files;

$reflection = new ReflectionClass(moodle_page::class);
$reflectionProperty = $reflection->getProperty('_flatnav');
$reflectionProperty->setAccessible(true);
$nav = new \theme_ctufeet\flat_navigation_ext($PAGE);
$nav->initialise();
$reflectionProperty->setValue($PAGE, $nav);

$output = $PAGE->get_renderer('local_kos');
echo $output->header();
echo $html;
echo $output->footer();
