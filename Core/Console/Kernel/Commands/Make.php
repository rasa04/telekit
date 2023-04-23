<?php

namespace Core\Console\Kernel\Commands;

use Core\Env;

class Make extends CommandAbstract
{
    use Env;

    public function __construct($options, $argv)
    {
        $samples = $this->app_path() . "\Core\Console\Kernel\Samples\\";
        $responses = $this->app_path() ."\Responses\\";
        $database = $this->app_path() . "\database\\";

        if (isset($options["interaction"])) {
            $file = $samples . "Interaction.php";
            $new_file = $responses . "Interactions\\" . $options["interaction"] . ".php";
            $this->response(["file" => $new_file], copy($file, $new_file));
        }
        elseif (isset($options["trigger"])) {
            $file = $samples . "Trigger.php";
            $new_file = $responses . "Triggers\\" . $options["trigger"] . ".php";
            $this->response(["file" => $new_file], copy($file, $new_file));
        }
        elseif (isset($options["migration"])) {
            $file = $samples . "Migration.php";
            $new_file = $database . "migrations\\"
                . date("Y_m_d")
                . time()
                . "_create_${options['migration']}_table.php";
            $this->response(["file" => $new_file], copy($file, $new_file));
        }
        else {
            $this->response("NO COMMANDS", false);
        }
    }
}