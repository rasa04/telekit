<?php

namespace Core\Console\Commands;

abstract class CommandAbstract
{
    public function response($show, $check = true): void
    {
        var_dump($show);
        echo ($check) ? "--#SUCCESS#--" : "--#ERROR#--";
    }
}