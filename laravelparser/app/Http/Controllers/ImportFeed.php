<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Yaml\Yaml;

/*
 * TODO:
 * - Refactoring
 * - Validator
 * - Queue
 * - FTP Downloader+Parser
 * - Sql/NoSql, models Logic
 * - Recode into Node.js or Go ? (free resources if this is a prod web server).
 * - Mail admin if file is not correct or there´s errors
 */

class ImportFeed extends Command
{


    private $sources = array (
        'flub'      => 'yaml',
        'glorf'     => 'json',
       //filename   =>  type
    );


    private $feed_path = 'feed-exports/';


    public function parse($source = null)
    {

        $extension = $this->sources[$source];

        if(empty($extension)){
            consoleOuput('Source not found...','error');
        }

        $file = $this->feed_path.$source.".".$extension;

        if( !Storage::disk('custom')->exists($file)){
            consoleOuput('File not Found!. place files in storage/public/feed-exports', 'error');
            return false;
        }

        switch ($extension){
            case 'json':
                $this->importJson($source);
                break;
            case 'yaml':
                $this->importYaml($source);
                break;
            case 'ftp':
                consoleOuput('Ftp is not yet supported...','error');
                //implement https://laravel.com/docs/5.6/filesystem FTP driver
        }

        return true;

    }


    public function importJson($source){

        // Function to parse Json Files ----------

        $extension = $this->sources[$source];

        $file = $this->feed_path.$source.".".$extension;


        consoleOuput('Starting to import '.$file);

        //get file

        $json = json_decode(Storage::disk('custom')->get($file), true);


        if(!isset($json["videos"])){
            consoleOuput('Videos is not available or json is corrupt ?','error');
            return false;
        }

        //symfony progress bar

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count( $json["videos"] ));
        $progress->start();

        foreach ($json["videos"] as $video){

            $name = $video["title"];
            $url  = $video["url"];
            $tags = $video["tags"];


            $this->importRecord($name, $url, $tags);
            $progress->advance();
        }

        $progress->finish();

        return true;
    }


    public function importYaml($source){

        // Function to parse Yaml Files ----------

        $extension = $this->sources[$source];

        $file = $this->feed_path.$source.".".$extension;

        consoleOuput('Starting to import '.$file);

        //get file

        $yaml = Yaml::parse( Storage::disk('custom')->get($file) );

        if(!isset($yaml)){
            consoleOuput('Videos is not available or json is corrupt ?','error');
            return false;
        }

        //symfony progress bar
        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($yaml));
        $progress->start();

        foreach ($yaml as $video){

            $name = $video["name"];
            $url  = $video["url"];
            $tags = array();
            if(isset($video["labels"])){
                $tags = explode(',', $video["labels"]);
            }

            $this->importRecord( $name, $url, $tags );
            $progress->advance();
        }

        $progress->finish();

        return true;

    }

    public function importRecord( $name = null, $url = null, $tags = null ){

        if(!empty($name) && !empty($url)){

            try {

                if (isset($tags) && is_array($tags)) {
                    $tags = " TAGS: " . implode(', ', $tags);
                }

                consoleOuput("Importing: '" . $name . "' URL(" . $url . ")" . $tags?:'', 'purple');

                // sleep for dev purposes. Remove it..
                sleep(0.5);

                //  Todo:
                //  Add Model insert/update logic here
                //
                //  $video = New video(); $video->save($video); etc
                //

            }catch (Throwable $t){

                consoleOuput($t->getMessage());

            }

        }else{

            consoleOuput("Record not valid, data is missing in : '".json_encode($name, $url, $tags), 'warning');

        }
    }


    public function getChoices(){

        //returns available feeds to Artisan command
        return array_keys($this->sources);

    }

    //Unit testing
    public function CheckFileExists($source){

        $extension = $this->sources[$source];

        if(empty($extension)){
            return false;
        }

        $file = $this->feed_path.$source.".".$extension;

        if( !Storage::disk('custom')->exists($file)){
            consoleOuput('File not Found!. place files in storage/public/feed-exports', 'error');
            return false;
        }else{
            return true;
        }


    }


}
