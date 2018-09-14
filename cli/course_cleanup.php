<?php
define('CLI_SCRIPT', true);
require_once '../../../../config.php';
require_once('../vendor/autoload.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);

const debug = false;
global $courseid;
$courseid = count($argv) > 1 ? $argv[1] : null;

function only_header_page(stored_file $file) : bool {
    $content = $file->get_content();
    if (preg_match('/\A{{[^(}})]*}}\n\n======[^(======)]*======\n*({{[^(}})]*}})?\n*\z/', $content)) {
        echo '[OnlyHeader] Removing file ' . $file->get_filepath() . $file->get_filename() . PHP_EOL;
        if (!debug) {
            $file->delete();
        }
        return true;
    }
    return false;
}

function remove_founding(stored_file $file) : bool {
    if (
    ($file->get_filepath() == '/' && ($file->get_filename() == 'funding.txt' || $file->get_filename() == 'schedule.txt')) ||
    ($file->get_filepath() == '/classification/' && $file->get_filename() != 'start.txt' )
    ) {
        echo '[Funding] Removing funding page' . PHP_EOL;
        if (!debug) {
            $file->delete();
        }
        return true;
    }
    return false;
}


function remove_unused_pages(stored_file $file) : bool {
    $content = $file->get_content();
    if (strlen(trim($content))==0 ||preg_match('/\A({{[^(}})]*}})?\n*======[^(======)]*======\n*<note>[^<]*<\/note>\n*({{[^(}})]*}})?\n*\z/', $content)) {
        echo '[UnusedPage] Removing unused page' . PHP_EOL;
        if (!debug) {
            $file->delete();
        }
        return true;
    }
    return false;
}

function remove_unused_link_pages(stored_file $file) : bool {
    $content = $file->get_content();
    if (preg_match('/\A======[^(======)]*======\n\s*\*\s*\[\[[^(\]\]\)]*\]\]\n*\z/', $content)) {
        echo '[UnusedLinkPage] Removing unused link page' . PHP_EOL;
        if (!debug) {
            $file->delete();
        }
        return true;
    }
    return false;
}

function remove_wrong_namespaces(stored_file $file) : bool{
    $wrong=[
            '/student',
            '/en/student',
            '/team',
            '/en/team',
            '/playground',
            '/wiki',
            '/classification/student',
            '/classification/view',
            '/en/classification/student',
            '/en/classification/view'
    ];
    foreach ($wrong as $w)
    {
        if (strpos($file->get_filepath(), $w) === 0) {
            echo '[Playground] Removing wrong namespace' . PHP_EOL;
            if (!debug) {
                $file->delete();
            }
            return true;
        }
    }
    return false;
}

function search($dir) {
    $c = 0;
    foreach ($dir['subdirs'] as $subdir) {
        $c += search($subdir);
    }
    if ((count($dir['files']) + count($dir['subdirs']) - $c) === 0) {
        if (!debug) {
            if($dir['dirfile']!=null)
                $dir['dirfile']->delete();
        }
        echo '[UnusedFolder] Removing empty folder' . PHP_EOL;
        return 1;
    }
    return 0;
}

function run($courseid) {
    $fs = get_file_storage();
    $files = $fs->get_area_files(context_course::instance($courseid)->id, 'format_wiki', 'pages', 0, "itemid, filepath, filename",
            false);
    foreach ($files as $file) {
        if (only_header_page($file) || remove_founding($file) || remove_wrong_namespaces($file) || remove_unused_pages($file) || remove_unused_link_pages($file) ) {
            continue;
        }
    }
    $dirs = $fs->get_area_tree(context_course::instance($courseid)->id, 'format_wiki', 'pages', 0);

    search($dirs);
}
if($courseid==null)
{
    global $DB;
    $courses=$DB->get_records('course',['format'=>'wiki']);
    foreach ($courses as $c)
        run($c->id);
}
else
    run($courseid);