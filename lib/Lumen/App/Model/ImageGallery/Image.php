<?php

namespace App\Model\ImageGallery;

use Logger;
use App\AbstractModel;

class Image extends AbstractModel {

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
    ]
  ];

  const CHILD_CLASS_MAP = [
    "image_print_options" => "",
    "image_variants"      => ""
  ];


  public function get_image_variants() {
    Logger::debug("Getting all image variants for image id ". $this->id->to_int());
    $result = DBI::get_image_variants_by_image_id((int) $this->id);
    
    if($result) {
      
    } else {

    }
  }

}