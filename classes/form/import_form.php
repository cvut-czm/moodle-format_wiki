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

namespace format_wiki\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class import_form extends \moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        $form = $this->_form;
        $form->addElement('checkbox', 'deleteoldwiki', get_string('delete_old_wiki', 'format_wiki'));
        $form->addElement('checkbox', 'overwrite', get_string('overwrite_files', 'format_wiki'));
        $form->addElement('filepicker', 'pagefile', get_string('pagefile', 'format_wiki'), null,
                ['accepted_types' => '*']);
        $form->addElement('filepicker', 'mediafile', get_string('mediafile', 'format_wiki'), null,
                ['accepted_types' => '*']);

        $this->add_action_buttons();
    }
}