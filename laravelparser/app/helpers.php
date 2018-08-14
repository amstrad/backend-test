<?php
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


if (!function_exists("consoleOuput")) {

    /**
     * Write on Console
     *
     * @param string $type
     * @param string $message
     * @return void|\Exception
     */

    function consoleOuput( string $message,  $type = "info")
    {
        $arr = [
            "error" => "error",
            "warning" => "comment",
            "info" => "info",
            "purple" => "question"
        ];

        if (in_array($type, ['error', 'warning', 'info', "purple"])) {
            try{
                $output = new Symfony\Component\Console\Output\ConsoleOutput();
                $output->writeln("<{$arr[$type]}>{$message}</{$arr[$type]}>");
                Log::channel('importfeed')->info($message);
            }catch (Throwable $t){
                echo "Caught Exception ('{$t->getMessage()}')\n{$t}\n";
                Log::channel('importfeed')->error($message.' Exception '.$t->getMessage() );
            }

        }
    }
}

