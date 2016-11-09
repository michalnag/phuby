<?php

namespace App\Model;

use App\AbstractModel;

class ImageGallery extends AbstractModel {
  
  const ATTRIBUTE_MAP = [
    "id" => [
      "type" => "attribute",
      "attribute_class" => "IDAttr",
      "attribute_options" => []
    ],
    "title" => [
      "type" => "attribute",
      "attribute_class" => "StringAttr",
      "attribute_options" => [
        "min" => 6,
        "max" => 60
      ]      
    ],
    "url_title" => [
      "type" => "attribute",
      "attribute_class" => "StringAttr",
      "attribute_options" => [
        "min" => 6,
        "max" => 60,
        "allow_spaces" => false
      ]      
    ],
    "description" => [
      "type" => "attribute",
      "attribute_class" => "TextAttr"     
    ],
    "dtm_added" => [
      "type" => "attribute",
      "attribute_class" => "DateTimeAttr"     
    ],
    "published" => [
      "type" => "attribute",
      "attribute_class" => "BooleanAttr"     
    ]
  ];

  const CHILD_CLASS_MAP = [
    "images" => "\App\Model\ImageGallery\ImageCollection"
  ];

}