<?php

namespace HeimrichHannot\Typort;

class Importer extends \Backend
{
    public $objModule;

    public function __construct($objModule)
    {
        $this->objModule = $objModule;
    }

    public function run()
    {
        return false;
    }
}