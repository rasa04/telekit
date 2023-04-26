<?php

namespace Core\Console\Commands;

use Core\Env;

class Make extends CommandAbstract
{
    use Env;

    public function __construct($options, $argv)
    {
        $samplesPath = $this->app_path() . "\Core\Console\Samples\\";
        $responsesPath = $this->app_path() ."\Responses\\";
        $databasePath = $this->app_path() . "\database\\";

        if (isset($options["interaction"])) {
            $file = $samplesPath . "Interaction.php";
            $new_file = $responsesPath . "Interactions\\" . $options["interaction"] . ".php";
            $this->response(["file" => $new_file], copy($file, $new_file));
        }
        elseif (isset($options["trigger"])) {
            $file = $samplesPath . "Trigger.php";
            $new_file = $responsesPath . "Triggers\\" . $options["trigger"] . ".php";
            $this->response(["file" => $new_file], copy($file, $new_file));
        }
        elseif (isset($options["model"])) {
            $file = $samplesPath . "Model.php";
            $new_file = $databasePath . "models\\" . $options["model"] . ".php";
            $this->response(["file" => $new_file], copy($file, $new_file));
        }
        elseif (isset($options["migration"])) {
            $file = $samplesPath . "Migration.php";
            $new_file = $databasePath . "migrations\\"
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