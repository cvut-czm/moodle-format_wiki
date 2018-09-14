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

namespace Markup\Edux;

defined('MOODLE_INTERNAL') || die();

class Comment extends \WikiRenderer\Block {
    public $type = 'none';

    protected $closeTagDetected = false;

    protected $_args = null;

    public function isStarting($string) {
        if (preg_match('/\/\*(.*)$/', $string, $m)) {
            $this->_args = $m;
            if (preg_match('/(.*)\*\/(\s*)$/', $m[1], $m2)) {
                $this->_closeNow = true;
                $this->_detectMatch = $m2[1];
                $this->closeTagDetected = true;
            } else {
                $this->_closeNow = false;
                $this->_detectMatch = $m[1];
            }

            return true;
        } else {
            return false;
        }
    }

    public function open() {
        $this->closeTagDetected = false;
        parent::open();
    }

    public function validateLine() {
        if (!$this->closeTagDetected || $this->_detectMatch != '') {
            $this->generator->addLine($this->_detectMatch);
        }
    }

    public function isAccepting($string) {
        if ($this->closeTagDetected) {
            return false;
        }

        $this->_args = null;
        if (preg_match('/(.*)\*\/(\s*)$/', $string, $m)) {
            $this->_detectMatch = $m[1];
            $this->closeTagDetected = true;
        } else {
            $this->_detectMatch = $string;
        }

        return true;
    }
}
