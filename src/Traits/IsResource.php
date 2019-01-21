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
        $this->populate(static::DBI()::findById($this->id));
    }

    public function save() {
        if ($this->id->get()) {
            return static::DBI()::update($this->getUpdateData());
        } else {
            $this->set_attr('id', (static::DBI()::create($this->getCreateData())));
            return $this;
        }
    }

    public function delete() {
        return static::DBI()::delete($this->id->get());
    }

}