<?php

namespace PHuby\Interfaces;

interface ResourceDBIInterface {
    
    public static function findById($id): ?array;
    public static function create($params);
    public static function update($params);
    public static function delete($id);
}