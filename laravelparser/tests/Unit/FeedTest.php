<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\ImportFeed;


class FeedTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test()
    {
        $import = new ImportFeed();

        $feeds = $import->getChoices();
        foreach ($feeds as $feed){
            //Check if files are reachable
            $this->assertTrue( $import->CheckFileExists($feed ) );

        }

        //Todo - More testing. Ex: check if Json is valid, check if Yaml is valid, etc etc

    }
}
