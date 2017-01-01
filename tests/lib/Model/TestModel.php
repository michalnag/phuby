<?php

namespace Model;

use PHuby\AbstractModel;

class TestModel extends AbstractModel {

  public 
    $id,
    $dtm_added;

  const ATTRIBUTE_MAP = [
    'id'        => [
      "attribute_class" => "\PHuby\Attribute\IntAttr"
    ],
    'dtm_added'        => [
      "attribute_class" => "\PHuby\Attribute\DateTimeAttr"
    ]
  ];

  const CHILD_CLASS_MAP = [
    "collection" => "\Model\TestModelCollection"
  ];
}