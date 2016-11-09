<?php

namespace App\Model\ImageGallery;

use Logger;
use Error;
use App\AbstractModelCollection;

class ImageCollection extends AbstractModelCollection {
   
   const COLLECT_CLASS = [
      "name" => "\App\Model\ImageGallery\Image"
   ];

}