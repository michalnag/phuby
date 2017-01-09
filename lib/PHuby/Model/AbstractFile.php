<?php
/**
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 * 
 * Abstract class that sits on the base of every model inside File namespace.
 * It contains common functionality to all file types such as upload, remove etc.
 * File type specific functionality, e.g. image resizing sits inside relevant model type.
 */

namespace PHuby\Model;

use PHuby\AbstractModel;

abstract class AbstractFile extends AbstractModel {

  public 
    $filename,
    $location;


}