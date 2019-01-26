<?php

namespace PHuby\Interfaces;

interface ResourceModelInterface {
    
    public static function DBI();

    public function getCreateData(): array;
    public function getUpdateData(): array; 
}