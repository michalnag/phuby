<?php

namespace PHuby\Traits;

trait IsResource {
    
    protected static function instantiate($rawData) {
        return $rawData ? new self($rawData) : null;
    }

    public static function find($id) {
        return self::instantiate(static::DBI()::findById($id));
    }

    public function refresh() {
        $this->populate(static::DBI()::findById($id));
    }

}