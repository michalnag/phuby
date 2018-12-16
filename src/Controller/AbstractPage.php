<?php
/**
 * Base Abstract class to sit in the bottom of each controller representing
 * a viewable page.
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Controller
 */

namespace PHuby\Controller;

use PHuby\Config;
use PHuby\AbstractController;
use PHuby\Helpers\Utils\ArrayUtils;
use PHuby\Logger;

use Twig_Autoloader;
use Twig_Loader_Filesystem;
use Twig_Environment;

abstract class AbstractPage extends AbstractController {

  protected $twig;
  protected $template, $template_vars = [];

  protected function setup_twig() {
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
    return $this;
  }

  protected function render($custom_params = null) {
    // Check parameters
    $params = [
      "template" => $this->template,
      "template_vars" => $this->template_vars
    ];

    if($custom_params !== null) {
      // We have custom params set
      if (is_string($custom_params)) {
        // If string, this will be a template
        $params['template'] = $custom_params;
      } elseif (is_array($custom_params)) {
        $params = array_merge($params, $custom_params);
      }
    }

    // Make sure that the Twig is setup correctly
    $this->setup_twig();
    return $this->twig->render($params["template"], $params["template_vars"]);
  }
}
