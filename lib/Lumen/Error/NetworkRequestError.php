<?php

namespace Lumen\Error;

use Lumen\AbstractError;

class NetworkRequestError extends AbstractError {

  const 
    EC_MISSING_PARAMETER      = 1,
    EC_INVALID_PARAMETER      = 2;

}