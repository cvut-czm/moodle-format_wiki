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

use tool_monitor\output\managesubs\subs;

defined('MOODLE_INTERNAL') || die();

class wiki_url {
    /**
     *  Moodle page format:
     *   - Must start with a-Z0-9
     *   - Link on folder redirect on /folder/start
     *   - Cannot have wildcard and dot notation
     *
     *
     */

    /**
     * @var \context $context
     */
    private static $context;
    private static $page;

    public static function set_current_context(\context $context) {
        self::$context = $context;
    }
    public static function get_current_context() : \context_course {
        return self::$context;
    }

    public static function set_current_page(string $page) {
        self::$page = $page;
    }

    private $url;
    private $ismedia = false;

    private function __construct(string $page, bool $ismedia = false) {
        $this->url = $page;
        $this->ismedia = $ismedia;
    }

    public static function from_moodle_url(string $page) : wiki_url {
        return new wiki_url($page);
    }

    public static function from_wiki_link(string $link) : wiki_url {
        $prefix = self::$page;
        if (strpos($link, '.') !== 0) {
            $link= self::cleanup($link);
            return new wiki_url(strpos($link,'/')===0?$link:($prefix . $link));
        }
        while (strpos($link, '..:') === 0) {
            $prefix = substr($prefix, 0, strrpos($prefix, '/'));
            $link = substr($link, 3);
        }
        if (strrpos($link, '.:') === 0) {
            $link = substr($link, 2);
        }
        $link= self::cleanup($link);
        return new wiki_url(strpos($link,'/')?$link:($prefix . $link));
    }

    private static function cleanup(string $url) : string {
        $url = str_replace(':', '/', $url);
        if (substr($url, -1) === '/') {
            $url .= 'start';
        }
        return $url;
    }

    public static function from_media_link(string $link) : wiki_url {
        $object = self::from_wiki_link($link);
        $object->ismedia = true;
        return $object;
    }

    public function get_page() : string {
        return $this->url;
    }

    public function is_media() : bool {
        return $this->ismedia;
    }

    public function get_media_url() {
        if ($this->ismedia) {
            return new \moodle_url('/course/format/wiki/mediafile.php', ['id' => self::$context->instanceid, 'path' => $this->url]);
        } else {
            return null;
        }
    }

    /***
     * @return bool|\stored_file
     */
    public function get_resource() {
        $fs = get_file_storage();
        $index = strrpos($this->url, '/');
        $folder = substr($this->url, 0, $index+1);
        $file = substr($this->url, $index + 1);
        $tree = $fs->get_area_tree(self::$context->id, 'format_wiki', 'pages', 0);
        if ($this->ismedia) {
            $file= $fs->get_file(self::$context->id, 'format_wiki', 'media', 0, $folder, $file);
                        return $file;
        } else {
            return $fs->get_file(self::$context->id, 'format_wiki', 'pages', 0, $folder . '/', $file . '.txt');
        }
    }
}