<?php
/**
 * AbstractCore
 * Every Controller / Model / ModelCollection / Process / Network inherits
 * this class. All functionality found here is available in namespaces listed.
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use PHuby\Logger;
use PHuby\Helpers\Utils;

abstract class AbstractCore {

  const 
    CLASS_TYPE_MODEL = 1,
    CLASS_TYPE_COLLECTION = 2;


  /**
   * Method gets the class type by interpreting CLASS_TYPE constant set on the class
   * @return integer representing a class type (set on the AbsstractCore), null if undefined
   * @todo check if the constant is set on the class
   */
  public function get_class_type() {
    return $this::CLASS_TYPE;
  }


  /**
   * Method checks whether the class is a collection class or not
   * @return boolean true is current class is a collection class, false otherwise
   */
  protected function is_collection_class() {
    return $this->get_class_type() == self::CLASS_TYPE_COLLECTION;
  }


}