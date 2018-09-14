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

namespace Generator\MoodleHtml;

defined('MOODLE_INTERNAL') || die();

class NoteUl implements \WikiRenderer\Generator\InlineGeneratorInterface {

    public function __construct(\WikiRenderer\Generator\Config $config) {
    }

    /**
     * says if it has no content.
     */
    public function isEmpty() {
        return false;
    }

    private $closing=false;
    public function setClosing(bool $value)
    {
        $this->closing=$value;
    }

    /**
     * @return string
     */
    public function generate() {
        return $this->closing?'</ul>':'<ul>';
    }
}