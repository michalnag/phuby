<?php
/**
 * Interface for Attribute Classes
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 */

namespace PHuby\Attribute;

interface AttributeInterface {

  const 
    ATTRIBUTE_CLASS = 'class',
    MODEL_CLASS = 'child_class',
    COLLECTION_CLASS = 'collection_class';

  public function set($value);
  
  public function get();
  
  public function to_db_format();

  public function __toString();

}