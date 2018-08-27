<?php
/**
 * It's TCP server
 * User: moyo
 * Date: 2018/7/31
 * Time: 3:45 PM
 */

namespace Carno\Socket\Contracts;

use Carno\Net\Contracts\Serving;
use Carno\Net\Contracts\TCP;

interface TServer extends TCP, Serving
{

}
