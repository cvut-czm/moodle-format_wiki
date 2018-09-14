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

class format_wiki_external extends external_api {
    public static function parse_parameters() {
        return new external_function_parameters([
                'markup_type' => new external_value(PARAM_ALPHANUMEXT, 'Type of markup'),
                'id' => new external_value(PARAM_INT, 'Course id'),
                'page' => new external_value(PARAM_RAW, 'Path'),
                'text' => new external_value(PARAM_RAW, 'Markup input')
        ]);
    }

    public static function parse($markup_type, $id, $page, $text) {
        $params = self::validate_parameters(self::parse_parameters(),
                ['markup_type' => $markup_type, 'text' => $text, 'id' => $id, 'page' => $page]);

        $context = context_course::instance_by_id($id);
        \format_wiki\wiki_url::set_current_context($context);
        \format_wiki\wiki_url::set_current_page($page);
        $markupConfig = new  Markup\Edux\Config($context, (new moodle_url('/course/view.php', ['id' => $id])) . '&page=%s');
        $genConfig = new \Generator\MoodleHtml\Config();
        $generator = new \Generator\MoodleHtml\Document($genConfig);
        $wr = new \WikiRenderer\Renderer($generator, $markupConfig);
        return $wr->render($text);
    }

    public static function parse_returns() {
        return new external_value(PARAM_RAW, 'HTML output of inputed markdown');
    }
}