<?php

namespace Core\Console\Kernel\Commands;

abstract class CommandAbstract
{
    public function response($show, $check = true): void
    {
        var_dump($show);
        echo ($check) ? "--#SUCCESS#--" : "--#ERROR#--";
    }
}