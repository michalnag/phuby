<?php

namespace App\Controller;

use App\Config;
use App\AbstractController;

abstract class AbstractPage extends AbstractController {

  private $twig;

  public $main_nav = [
    "contact" => [
      "label" => "Contact",
      "href" => "/contact",
      "title" => "###"
    ]
  ];

  public function __construct(Array $params = []) {
    $this->setup_twig();
  }

  private function setup_twig() {
    \Twig_Autoloader::register();
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../../../src/templates/');
    $this->twig = new \Twig_Environment($loader, array('debug' => true));
  }

  protected function render() {
    return $this->twig->render($this->template, $this->template_vars);
  }
}