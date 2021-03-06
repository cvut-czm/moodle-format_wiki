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
 * Porter helper for converting tgz dokuwiki export to course.
 *
 *
 * @package    format
 * @category   wiki
 * @copyright  2018 CVUT CZM, Jiri Fryc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_wiki;

use splitbrain\PHPArchive\FileInfo;
use splitbrain\PHPArchive\Tar;

defined('MOODLE_INTERNAL') || die();

class porter {

    /** @var \context_course $context */
    private $context;

    /** @var string $temppage Temporary path to page tgz. */
    private $temppage;

    /** @var string $tempmedia Temporary path to media tgz. */
    private $tempmedia=null;

    /**
     * Setter for context.
     *
     * @param \context_course $context
     * @return porter Return self. (Fluent API)
     */
    public function set_context(\context_course $context) : porter {
        $this->context = $context;
        return $this;
    }

    /**
     * Setter for page tgz.
     *
     * @param string $pathtofile
     * @return porter Return self. (Fluent API)
     */
    public function set_pages(string $pathtofile) : porter {
        $this->temppage = $pathtofile;
        return $this;
    }

    /**
     * Setter for media tgz.
     *
     * @param string $pathtofile
     * @return porter Return self. (Fluent API)
     */
    public function set_media(string $pathtofile) : porter {
        $this->tempmedia = $pathtofile;
        return $this;
    }

    /**
     * Deletes old files.
     *
     * @return porter Return self. (Fluent API)
     */
    public function delete_old() : porter {

        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'format_wiki', 'wiki', 0);
        $fs->delete_area_files($this->context->id, 'format_wiki', 'pages', 0);
        $fs->delete_area_files($this->context->id, 'format_wiki', 'media', 0);
        return $this;
    }

    /**
     * Cleanup temp files.
     *
     * @return porter Return self. (Fluent API)
     */
    public function cleanup() : porter {
        unlink($this->temppage);
        unlink($this->tempmedia);
        return $this;
    }

    static function rmdirr($dirname) {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }
        $dir = dir($dirname);
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            self::rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
        $dir->close();
        return rmdir($dirname);
    }

    /**
     * Port data from tgz.
     *
     * @param bool $overwrite If files should be overwriten.
     * @return porter Return self. (Fluent API)
     */
    public function port(bool $overwrite,int $type=0) : porter {
        global $CFG;
        $path = $CFG->tempdir . '/wiki/' . rand(0, 9999).'/';
        mkdir($path, 0777, true);
        $tar = new Tar();
        $tar->open($this->temppage);
        $tar->extract($path);
        if ($this->tempmedia !== null) {
        $tar = new Tar();
        $tar->open($this->tempmedia);
        $tar->extract($path);
        }
        $coursecode = scandir($path)[2]; // Assuming valid notation, otherwise we catch exception above port function.
        $basepath = $path . '/' . $coursecode . '/data';
        $files = [];
        $this->walk($basepath . '/pages', '', $files);

        $fs = get_file_storage();
        foreach ($files as $file) {
            $pp=null;
            switch ($type)
            {
                default:
                case 0:
                $pp=$file[0];
                    break;
                case 1:
                    if(strpos($file[0],'/en')!==0 && strpos($file[0],'en')!==0)
                        break;
                    $pp=substr($file[0],strpos($file[0],'en')+2);
                    break;
                case 2:
                    if(strpos($file[0],'/parttime')!==0 && strpos($file[0],'parttime')!==0)
                        break;
                    $pp=substr($file[0],strpos($file[0],'parttime')+8);
                    break;
            }
            if($pp===null)
                continue;
            $fullpath = $basepath . '/pages' . $file[0] . '/' . $file[1];
            $fs->create_file_from_string([
                    'contextid' => $this->context->id,
                    'component' => 'format_wiki',
                    'filearea' => 'pages',
                    'itemid' => 0,
                    'filepath' => $pp . '/',
                    'filename' => $file[1],
                    'timecreated' => time(),
                    'timemodified' => time()
            ], file_get_contents($fullpath));

        }

        if ($this->tempmedia !== null) {
            $files = [];
            $this->walk($basepath . '/media', '', $files);

            $fs = get_file_storage();
            foreach ($files as $file) {
                if(strpos($file[0],'latex')===0 || strpos($file[0],'latex')===1)
                    continue;
                $fullpath = $basepath . '/media' . $file[0] . '/' . $file[1];
                $fs->create_file_from_string([
                        'contextid' => $this->context->id,
                        'component' => 'format_wiki',
                        'filearea' => 'media',
                        'itemid' => 0,
                        'filepath' => $file[0] . '/',
                        'filename' => $file[1],
                        'timecreated' => time(),
                        'timemodified' => time()
                ], file_get_contents($fullpath));

            }
        }
        //TODO self::rmdirr($basepath);
        return $this;
    }

    /**
     * Walker that recursivly iterate over folder.
     *
     * @param string $basepath Base path
     * @param string $currentpath Current path
     * @param string[][] $files Files in directories
     */
    private function walk(string $basepath, string $currentpath, array &$files) : void {
        $path = $basepath . '/' . $currentpath;
        $directorycontent = scandir($path);
        foreach ($directorycontent as $content) {
            if ($content[0] == '.') {
                continue;
            } else if (is_dir($path . '/' . $content)) {
                $this->walk($basepath, $currentpath . '/' . $content, $files);
            } else {
                $files[] = [$currentpath, $content];
            }
        }
    }

}