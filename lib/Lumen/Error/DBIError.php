<?php

namespace Lumen\Error;

use Lumen\AbstractError;

class DBIError extends AbstractError {

  const
    /** DB Related - reserved between 1 and 99 */
    EC_DB_CONNECTION_FAILED     = 1,
    EC_DB_INSERT_FAILED         = 2,
    EC_DB_SELECT_FAILED         = 3,
    EC_DB_UPDATE_FAILED         = 4,
    EC_DB_DELETE_FAILED         = 5,
    EC_DB_DUPLICATED_ENTRY      = 6,
    EC_DB_OTHER                 = 99;

    
}