<?php
define('CLI_SCRIPT',true);
require_once '../../../../config.php';
require_once('../vendor/autoload.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);

$files = glob('/home/frycjiri/edux/*_pages.tgz');

$context2=new \local_kos\kos_context();
$i=0;
foreach($files as $file)
{

    $i++;
    $code= substr($file,strrpos($file,'/')+1,-10);
    $path= substr($file,0,strrpos($file,'/')+1);

    try {
        $data = \local_kos\kosapi\entities\course::fetchCourse($code, $context2, 'B181');
    }
    catch (Exception $e)
    {
        continue;
    }

    $pages_file=$file;
    $media_file=$path.$code.'_media.tgz';

    if(file_exists($media_file) && filesize($media_file)>536870912)
    {
        echo 'Skipping large course '.$code.' is ported. ('.$i.')'.PHP_EOL;
        continue;
    }


    $test=\local_kos\entity\course::get($code,'code');
    if(count($test->get_instances())>0)
    {
        echo 'Already loaded '.$code.PHP_EOL;
        $crs=$test->get_instance(\local_kos\entity\semester::get_current());
    }
    else {
        $crs = \local_kos\course_builder::create()
                ->set_semester(\local_kos\entity\semester::get_current())
                ->add_kos_courses([$code])
                ->set_main_course($code)
                ->create_empty()
                ->set_use_wiki(true)
                ->build();
    }
    $context = context_course::instance($crs->course_id);
    $porter = new \format_wiki\porter();
    $porter->set_context($context);
    $porter->set_pages($pages_file);
    if(file_exists($media_file))
    $porter->set_media($media_file);
    $porter->delete_old();
    $porter->port(true);
    echo 'Course '.$code.' is ported. ('.$i.')'.PHP_EOL;
}