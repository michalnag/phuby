<?php

namespace PHuby\Traits;

use PHuby\DBI\ResourceDBI;

trait IsResource {

    use HasNumericId;

    protected static function instantiate($rawData) {
        return $rawData ? new self($rawData) : null;
    }

    public static function find($id) {
        return self::instantiate(ResourceDBI::findById(static::$table, $id));
    }

    public function refresh() {
        $this->populate(ResourceDBI::findById(static::$table, $this->id));
    }

    public function save() {
        if ($this->id->get()) {
            return ResourceDBI::update(static::$table, $this->getUpdateData());
        } else {
            $this->set_attr('id', (ResourceDBI::insert(static::$table, $this->getCreateData())));
            return $this;
        }
    }

    public function delete() {
        return ResourceDBI::delete(static::$table, $this->id->get());
    }

}