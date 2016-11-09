<?php

namespace App\Controller\Public;

use App\Controller\PageCommon;
use Logger;
use Network\Request;

class HomePageController extends PageCommon {
    
  protected $template = "public/pages/home_page.twig";
  public $template_vars = [];

  public function get() {


  }

}