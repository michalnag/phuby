<?php

namespace PHuby\Controller;

use PHuby\Config;
use PHuby\AbstractController;
use PHuby\Helpers\Utils\ArrayUtils;
use PHuby\Logger;

use Twig_Autoloader;
use Twig_Loader_Filesystem;
use Twig_Environment;

abstract class AbstractPage extends AbstractController {

  private $twig;

  private function setup_twig() {
    if(!$this->twig && !$this->twig instanceof Twig_Environment) {
      Twig_Autoloader::register();
      $loader = new Twig_Loader_Filesystem(
        Config::get_data("core:app_root") . DIRECTORY_SEPARATOR . Config::get_data("core:templates_dir"));
      $this->twig = new Twig_Environment($loader, array('debug' => true));
    }
    return true;
  }

  public function add_template_vars($str_keymap, $data) {
    // Assign vars
    ArrayUtils::add_data($str_keymap, $this->template_vars, $data);
    Logger::debug("Added following data to template_vars in keymap $str_keymap" . json_encode($data));
    return true;
  }

  protected function render(Array $params = null) {
    // Check parameters
    if(!$params) {
      $params = [
        "template" => $this->template,
        "template_vars" => $this->template_vars
      ];
    }

    // Make sure that the Twig is setup correctly
    $this->setup_twig();
    return $this->twig->render($params["template"], $params["template_vars"]);
  }
}