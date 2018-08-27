<?php
/**
 * EV Socket
 * User: moyo
 * Date: 02/08/2017
 * Time: 10:09 AM
 */

namespace Carno\Socket;

use Carno\Net\Address;
use Carno\Net\Events;
use Carno\Socket\Contracts\TClient;
use Carno\Socket\Contracts\TServer;
use Carno\Socket\Powered\Swoole\Client;
use Carno\Socket\Powered\Swoole\Server;

class Socket
{
    /**
     * @param Address $address
     * @param Events $events
     * @param Options $options
     * @return TClient
     */
    public static function connect(Address $address, Events $events, Options $options = null) : TClient
    {
        return (new Client)->connect($address, $events, $options);
    }

    /**
     * @param Address $address
     * @param Events $events
     * @param Options $options
     * @return TServer
     */
    public static function listen(Address $address, Events $events, Options $options = null) : TServer
    {
        return (new Server)->listen($address, $events, $options);
    }
}
