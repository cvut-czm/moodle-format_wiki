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
use theme_ctufeet\navigation\course\course_activities_module;
use theme_ctufeet\navigation\course\course_nav_module;

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
class format_wiki extends format_base {

    static function flatnavnode_builder(string $text, string $shorttext, string $key, $action = null, $parent = null, $icon = null,
            string $type = null) : array {
        $data = ['text' => $text, 'shorttext' => $shorttext, 'action' => $action, 'icon' => $icon, 'type' => $type, 'key' => $key,
                'parent' => $parent];
        foreach ($data as $k => $v) {
            if ($v == null) {
                unset($data[$k]);
            }
        }
        return $data;
    }

    /**
     * !!! This is only for our private theme plugin. !!!
     *
     * We have custom flatnav rendering, that allow us better control.
     *
     */
    public function extend_flat_navigation(course_nav_module $flat_navigation) {
        global $PAGE;
        $courseid = $this->courseid;
        $createpage = get_string('create:page', 'format_wiki');
        $createfolder = get_string('create:folder', 'format_wiki');

        $filter=new \filter_multilang($PAGE->context,[]);
        $name=$filter->filter($this->get_course()->shortname);
        $header =
                new flat_navigation_node(self::flatnavnode_builder($name, $name,
                        'coursename',
                        null, null,
                        new pix_icon('i/course', '')), 0);
        $header->type = 'header';
        $header->set_showdivider(true);
        $flat_navigation->nav->add($header);

        $module=new course_activities_module($flat_navigation->nav);
        $module->set_media_location('format_wiki','media',0);
        $module->apply();
        //        $this->walk_tree_structure($flat_navigation); DEPRECATED
        $sidebar = new \format_wiki\sidebar($courseid, -1);
        $sidebar->set_current_page(optional_param('page', '/start', PARAM_RAW));
        $sidebar->generate_to($flat_navigation->nav);
        $flat_navigation->nav->add(new flat_navigation_node(self::flatnavnode_builder($createpage, $createpage, 'wiki_create_page',
                new moodle_url('/course/format/wiki/newpage.php', ['id' => $courseid]), null, new pix_icon('t/add', '')), 1));

    }

    private function walk_tree_structure(flat_navigation $flat_navigation) {
        $fs = get_file_storage();
        $tree = $fs->get_area_tree(context_course::instance($this->courseid)->id, 'format_wiki', 'pages', 0);
        $this->recursion($flat_navigation, $tree, '', null, 0, false);
    }

    private function node_create_for_file($name, $path, $indent, $parent, $hidden) {
        global $PAGE;
        $pos = strpos($name, '.txt');
        if ($pos > 0) {
            $name = substr($name, 0, $pos);
        }
        $trans = get_string_manager()->string_exists('wiki:' . $name, 'format_wiki') ? get_string('wiki:' . $name, 'format_wiki') :
                $name;
        $node = new flat_navigation_node(self::flatnavnode_builder(
                $trans, $trans, 'wiki_page/' . $path . '/' . $name,
                (new moodle_url('/course/view.php', ['id' => $PAGE->course->id])) . '&page=' . $path . '/' . $name, $parent),
                $indent);
        $node->hidden = $hidden;
        return $node;
    }

    private $ignore_list_file = [
            'sidebar'
    ];
    private $ignore_list_folder =
            [
                    'classification',
                    'harmonogram',
                    'harmonogram-test',
                    'team',
                    'news',
                    'av',
                    'wiki'
            ];

    private function recursion(flat_navigation $flat_navigation, $tree, $path, $parent, int $indent = 0, $hidden = true,
            $append = '') {
        foreach ($tree['subdirs'] as $subdir) {
            if (in_array($subdir['dirname'], $this->ignore_list_folder)) {
                continue;
            }
            $files = count($subdir['files']);
            $subdirs = count($subdir['subdirs']);
            if ($files == 1 && $subdirs == 0) {
                $filename = key($subdir['files']);
                if ($filename == 'start.txt' || $filename == $subdir['dirname'] . '.txt') {
                    $filename = '/start';
                } else {
                    $filename = '/' . $filename;
                }
                $flat_navigation->add($this->node_create_for_file($append . $subdir['dirname'] . $filename,
                        $path . '/' . $subdir['dirname'], $indent, $parent, $hidden));
            } else if ($files == 0 && $subdirs > 0) {
                foreach ($subdir['subdirs'] as $s) {
                    $this->recursion($flat_navigation, $s, $path . '/' . $subdir['dirname'] . $s['dirname'], $parent, $indent + 1,
                            $hidden, $subdir['dirname'] . '/');
                }
            } else if ($files == 0 && $subdirs == 0) {
                continue; // Skipping
            } else {
                $trans = get_string_manager()->string_exists('wiki:' . $append . $subdir['dirname'], 'format_wiki') ?
                        get_string('wiki:' . $append . $subdir['dirname'], 'format_wiki') : $append . $subdir['dirname'];
                $node = new flat_navigation_node(self::flatnavnode_builder(
                        $trans, $trans,
                        'wiki_folder/' . $path . '/' . $subdir['dirname'], null, $parent),
                        $indent);
                $node->isexpandable = true;
                $node->collapse = true;
                $node->hidden = $hidden;
                $flat_navigation->add($node);
                $this->recursion($flat_navigation, $subdir, $path . '/' . $subdir['dirname'], $node, $indent + 1);
            }
        }
        foreach ($tree['files'] as $name => $file) {

            if (in_array($name, $this->ignore_list_file)) {
                continue;
            }
            $flat_navigation->add($this->node_create_for_file($append . $name, $path, $indent, $parent, $hidden));
        }

    }
}
