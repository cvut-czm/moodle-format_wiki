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

namespace format_wiki;

defined('MOODLE_INTERNAL') || die();

class wysiwyg {

    /** Hold all loaded functions for current wysiwyg in order in which they are displayed. */
    private $functions = [];

    public static function create_default(string $name = '', string $content = '') {
        $new = new wysiwyg();
        $new->register_default();
        $new->set_content($content);
        $new->set_name($name);
        return $new;
    }

    protected static function gen_js_oval(string $name, string $code, string $code2 = null, string $text = 'TEXT') : string {
        if ($code2 === null) {
            $code2 = $code;
        }
        $l = strlen($code);
        $out = 'function ' . $name . '(id){' . PHP_EOL;
        $out .= '       var $txt = jQuery("#"+id);' . PHP_EOL . '
                        var caretPos = $txt[0].selectionStart;' . PHP_EOL . '
                        var caretEnd = $txt[0].selectionEnd;' . PHP_EOL . '
                        var textAreaTxt = $txt.val();' . PHP_EOL . '
                        var txtToAdd = (caretPos==caretEnd?\'' . $text . '\':textAreaTxt.substring(caretPos,caretEnd));' . PHP_EOL . '
                        $txt.val(textAreaTxt.substring(0, caretPos) + "' . $code . '" + txtToAdd+ "' . $code2 .
                '" + textAreaTxt.substring(caretEnd));' . PHP_EOL . '
                        $txt.focus();
                        $txt.selectRange(caretPos+' . $l . ',caretPos+txtToAdd.length+' . $l . ');}';
        return $out;
    }

    private function register_default() {
        $this->add_function('bold', get_string('wysiwyg:bold', 'format_wiki'), 'bold',
                self::gen_js_oval('wysiwyg_bold', '**'), get_string('wysiwyg:bold:tooltip', 'format_wiki'));
        $this->add_function('italic', get_string('wysiwyg:italic', 'format_wiki'), 'italic',
                self::gen_js_oval('wysiwyg_italic', '//'), get_string('wysiwyg:italic:tooltip', 'format_wiki'));
        $this->add_function('underline', get_string('wysiwyg:underline', 'format_wiki'), 'underline',
                self::gen_js_oval('wysiwyg_underline', '__'), get_string('wysiwyg:underline:tooltip', 'format_wiki'));
        $this->add_function('strikethrough', get_string('wysiwyg:strikethrough', 'format_wiki'), 'strikethrough',
                self::gen_js_oval('wysiwyg_strikethrough', '<del>', '</del>'),
                get_string('wysiwyg:strikethrough:tooltip', 'format_wiki'));

        $this->add_function('externallink', get_string('wysiwyg:externallink', 'format_wiki'), 'globe',
                self::gen_js_oval('wysiwyg_externallink', '[[', ']]', 'http://example.com|External Link'),
                get_string('wysiwyg:externallink:tooltip', 'format_wiki'));

        $this->add_function('unorderedlist', get_string('wysiwyg:unorderedlist', 'format_wiki'), 'list-ul',
                self::gen_js_oval('wysiwyg_unorderedlist', '  * ', ''),
                get_string('wysiwyg:unorderedlist:tooltip', 'format_wiki'));
        $this->add_function('orderedlist', get_string('wysiwyg:orderedlist', 'format_wiki'), 'list-ol',
                self::gen_js_oval('wysiwyg_orderedlist', '  - ', ''),
                get_string('wysiwyg:orderedlist:tooltip', 'format_wiki'));

        $this->add_function('horizontal', get_string('wysiwyg:horizontal', 'format_wiki'), 'ellipsis-h',
                self::gen_js_oval('wysiwyg_horizontal', "\\n----\\n", '', ''),
                get_string('wysiwyg:horizontal:tooltip', 'format_wiki'));
    }

    /**
     * Add new function to this instance of wysiwyg
     *
     * @param string $translated_string Translated string displayed on hover
     * @param string $icon Icon name form font awesome
     * @param callable $javascript Javascript generator
     * @param callable $callback Callback for ajax calls.
     * @return $this Fluent API
     */
    public function add_function(string $id, string $translated_string, string $icon, string $javascript, string $tooltip = null,
            callable $callback = null) : wysiwyg {
        $this->functions[] = ['id' => $id, 'text' => $translated_string, 'icon' => $icon, 'js' => $javascript,
                'callback' => $callback, 'tooltip' => $tooltip];
        return $this;
    }

    private $content;
    private $id;
    private $name;

    public function set_content(string $content) : wysiwyg {
        $this->content = $content;
        return $this;
    }

    public function set_name(string $name) : wysiwyg {
        $this->name = $name;
        $this->id = $name . '_input';
        return $this;
    }

    public function render() : string {
        $out = $this->render_buttons();
        $out .= '<textarea class="form-control" id="' . $this->id . '" name="' . $this->name . '" rows="20">' . $this->content .
                '</textarea>';
        $out .= '<script>';
        $out .= "";
        $out .= $this->render_javascript() . '</script>';
        return $out;
    }

    public function render_buttons() : string {
        $out = '<div class="btn-group mt-2 mb-2" role="group">';
        foreach ($this->functions as $fnc) {
            $tooltip = '';
            if ($fnc['tooltip'] != null) {
                $tooltip = 'data-toggle="tooltip" data-placement="bottom" title="' . $fnc['tooltip'] . '"';
            }
            $out .= '<button type="button" class="btn btn-sm btn-outline-primary" ' . $tooltip . '  onclick="wysiwyg_' .
                    $fnc['id'] .
                    '(\'' . $this->id . '\')"><i class="fa fa-' . $fnc['icon'] . '"></i></button>';
        }
        $out .= '</div>';
        return $out;
    }

    public function render_javascript() : string {
        $out = '';
        foreach ($this->functions as $fnc) {
            $out .= PHP_EOL . $fnc['js'];
        }
        return $out;
    }
}