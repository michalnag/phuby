<?php

namespace PHuby\Traits;

use PHuby\Attribute\IntAttr;

trait HasNumericId {

    protected $id;
    public $_attribute_map_numeric_id = [
        'id' => IntAttr::class,
    ];
}