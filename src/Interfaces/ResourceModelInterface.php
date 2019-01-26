<?php

namespace PHuby\Interfaces;

interface ResourceModelInterface {
    public function getCreateData(): array;
    public function getUpdateData(): array; 
}