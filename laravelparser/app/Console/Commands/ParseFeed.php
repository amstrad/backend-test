<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

//Write logic in controllers

use App\Http\Controllers\ImportFeed;

class ParseFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {source?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Videos from feed-exports';


    protected $importFeed;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( ImportFeed $importFeed  )
    {
        parent::__construct();

        $this->importFeed = $importFeed;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle( )
    {

        parent::__construct();

        $choices = $this->importFeed->getChoices();

        $source = $this->argument('source');

        if(empty($source) || !in_array($source, $choices)){
           $source = $this->choice('What feed do you want to import?', $choices, 0);
        }


        $this->importFeed->parse( $source );





    }
}
