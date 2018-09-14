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

use DiffMatchPatch\DiffMatchPatch;

defined('MOODLE_INTERNAL') || die();

class revisions {

    public static function is_changed(string $new, string $old) : bool {
        $dmp = new DiffMatchPatch();
        $diffs = $dmp->diff_main($new, $old);
        foreach ($diffs as $diff) {
            if ($diff[0] !== 0) {
                return true;
            }
        }
        return false;
    }

    public static function get_patch(string $old, string $new) : string {
        $dmp = new DiffMatchPatch();
        $diffs = $dmp->diff_main($old, $new);
        $patches = $dmp->patch_make($diffs);
        return $dmp->patch_toText($patches);
    }

    public static function apply_patch(string $new, string $patch) : string {
        $dmp = new DiffMatchPatch();
        $patches = $dmp->patch_fromText($patch);
        return $dmp->patch_apply($patches, $new)[0];
    }
}