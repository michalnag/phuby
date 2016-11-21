<?php

namespace Lumen\Controller;

use Lumen\Config;
use Lumen\AbstractController;

use Twig_Autoloader;
use Twig_Loader_Filesystem;
use Twig_Environment;

abstract class AbstractPage extends AbstractController {

  private static $twig;

  private static function setup_twig() {
    if(!self::$twig && !self::$twig instanceof Twig_Environment) {
      Twig_Autoloader::register();
      $loader = new Twig_Loader_Filesystem(
        Config::get_data("core:app_root") . DIRECTORY_SEPARATOR . Config::get_data("core:templates_dir"));
      self::$twig = new Twig_Environment($loader, array('debug' => true));
    }
    return true;
  }

  protected static function render(Array $params = null) {
    // Check parameters
    if(!$params) {
      $params = [
        "template" => static::$template,
        "template_vars" => static::$template_vars
      ];
    }

    // Make sure that the Twig is setup correctly
    self::setup_twig();
    return self::$twig->render($params["template"], $params["template_vars"]);
  }
}