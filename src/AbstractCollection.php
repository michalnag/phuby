<?php
/**
 * AbstractCollection sits as a base in every collection object
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use \IteratorAggregate;
use PHuby\Error;
use PHuby\AbstractCore;
use PHuby\Traits\SupportsFlatData;
use PHuby\Traits\SupportsCollection;


abstract class AbstractCollection extends AbstractCore implements IteratorAggregate {

  use SupportsFlatData;
  use SupportsCollection;

  const
    CLASS_TYPE = self::CLASS_TYPE_COLLECTION;

}