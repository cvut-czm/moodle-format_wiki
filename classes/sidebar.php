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

use flat_navigation;

defined('MOODLE_INTERNAL') || die();

class sidebar {

    private $courseid;
    private $userid;
    private $tree;
    private $page;

    public function __construct(int $courseid, int $userid) {
        $this->courseid = $courseid;
        $this->userid = $userid;
        $fs = get_file_storage();
        $this->tree = $fs->get_area_tree(\context_course::instance($courseid)->id, 'format_wiki', 'pages', 0);
    }

    public function set_current_page(string $page) {
        $this->page = explode('/', $page);
    }

    public function generate_to(flat_navigation $flat) {
        foreach ($this->tree['subdirs'] as $folder) {
            $this->iterate_folder($flat, $folder, null, 0, true);
        }
        foreach ($this->tree['files'] as $file) {
            if (!in_array($file->get_filename(), config::get_ignored_pages())) {
                $flat->add($this->file_to_nav($file, null, 0));
            }
        }
    }

    private function iterate_folder(flat_navigation $flat, array $folder, \flat_navigation_node $parent = null, int $indent = 0,
            $add = true) {
        if (in_array($folder['dirname'], config::get_ignored_folders()) || count($folder['files']) === 0 && count($folder['subdirs']) === 0) {
            return; // Skipping
        }

        if (count($folder['files']) === 1 && count($folder['subdirs']) === 0 && isset($folder['files']['start.txt'])) {
            // If this directory doesn´t contain any subfolder and only start.txt file is present.
            // Then draw this folder as start
            $nav = $this->file_to_nav($folder['files']['start.txt'], $parent, $indent, '_' . $folder['dirname']);
            $nav->text = $nav->shorttext = $this->get_string($folder['dirname']);

            $nav->isexpandable = false;

            if ($parent == null) {
                $nav->collapse = true;
            } else {
                $nav->hidden = $parent->collapse;
                $nav->collapse = $parent->collapse || $indent >= count($this->page) || $this->page[$indent] !== $nav->shorttext;
            }

            $flat->add($nav);
            return;
        }
        if ($add) {
            $nav = $this->folder_to_nav($folder, $parent, $indent);
            $nav->isexpandable = true;

            if ($parent == null) {
                $nav->collapse = true;
            } else {
                $nav->hidden = $parent->collapse;
                $nav->collapse = $parent->collapse || $indent >= count($this->page) || $this->page[$indent] !== $nav->shorttext;
            }

            $flat->add($nav);
        } else {
            $nav = null;
        }
        sort($folder['subdirs']); // For some reason subfolders and files are not sorted be any logical mean.
        sort($folder['files']);

        foreach ($folder['subdirs'] as $subfolder) {
            $this->iterate_folder($flat, $subfolder, $nav, $indent + 1);
        }
        foreach ($folder['files'] as $file) {
            if (!in_array($file->get_filename(), config::get_ignored_pages())) {
                $flat->add($this->file_to_nav($file, $nav, $indent + 1));
            }
        }

    }

    private function get_string(string $key): string {
        return get_string_manager()->string_exists('wiki:' . $key, 'format_wiki') ?
                get_string('wiki:' . $key, 'format_wiki') : ucfirst($key);
    }

    private function folder_to_nav(array $folder, \flat_navigation_node $parent = null, int $indent = 0): \flat_navigation_node {
        $text = $this->get_string($folder['dirname']);
        $data = ['text' => $text, 'shorttext' => $text,
                'key' => ($parent == null ? '' : $parent->key) . '_' . $text];
        if ($parent != null) {
            $data['parent'] = $parent;
        }
        return new \flat_navigation_node($data, $indent);
    }

    private function file_to_nav(\stored_file $file, \flat_navigation_node $parent = null, int $indent = 0,
            $key = ''): \flat_navigation_node {
        $text = $this->get_string(substr($file->get_filename(),0,-4));
        $data = ['text' => $text, 'shorttext' => $text,
                'key' => ($parent == null ? '' : $parent->key) . $key . '_' . $text.'--txt',
                'action' => (new \moodle_url('/course/view.php',
                        ['id' => $this->courseid, 'page' => $file->get_filepath() . substr($file->get_filename(),0,-4)]))->raw_out(false)];

        if ($parent != null) {
            $data['parent'] = $parent;
        }
        $nav = new \flat_navigation_node($data, $indent);
        if ($parent != null) {
            $nav->hidden = $parent->collapse;
        } else {
            $nav->hidden = false;
        }
        return $nav;
    }

}