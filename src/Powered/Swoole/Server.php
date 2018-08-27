<?php
/**
 * Swoole server
 * User: moyo
 * Date: 02/08/2017
 * Time: 10:10 AM
 */

namespace Carno\Socket\Powered\Swoole;

use Carno\Net\Address;
use Carno\Net\Events;
use Carno\Serv\Powered\Swoole\ServerBase;
use Carno\Socket\Connection;
use Carno\Socket\Contracts\TServer;
use Carno\Socket\Options;
use Carno\Socket\Powered\Swoole\Chips\Buffered;
use Carno\Socket\Powered\Swoole\Chips\Porting;
use Swoole\Server as SWServer;

class Server extends ServerBase implements TServer
{
    use Buffered, Porting;

    /**
     * @var array
     */
    protected $acceptEvs = [
        'bufferFull', 'bufferEmpty',
        'connect', 'close',
        'receive',
    ];

    /**
     * @var SWServer
     */
    private $server = null;

    /**
     * @param Address $address
     * @param Events $events
     * @param Options $options
     * @return TServer
     */
    public function listen(Address $address, Events $events, Options $options = null) : TServer
    {
        $this->server = $this->standardServerCreate(
            $address,
            $events,
            SWServer::class,
            $options ? $options->config() : []
        );
        return $this;
    }

    /**
     */
    public function serve() : void
    {
        $this->server->start();
    }

    /**
     */
    public function shutdown() : void
    {
        $this->server->shutdown();
    }

    /**
     * @param SWServer $server
     * @param int $fd
     * @param int $reactorID
     */
    public function evConnect(SWServer $server, int $fd, int $reactorID) : void
    {
        $client = $server->getClientInfo($fd);

        $this->events->notify(
            Events\Socket::CONNECTED,
            (new Connection)
                ->setLocal('0.0.0.0', $client['server_port'])
                ->setRemote($client['remote_ip'], $client['remote_port'])
                ->from($this)
        );
    }

    /**
     * @param SWServer $server
     * @param int $fd
     * @param int $reactorID
     */
    public function evClose(SWServer $server, int $fd, int $reactorID) : void
    {
        $client = $server->getClientInfo($fd);

        $this->events->notify(
            Events\Socket::CLOSED,
            (new Connection)
                ->setLocal('0.0.0.0', $client['server_port'])
                ->setRemote($client['remote_ip'], $client['remote_port'])
                ->from($this)
        );
    }

    /**
     * @param SWServer $server
     * @param int $fd
     * @param int $reactorID
     * @param string $data
     */
    public function evReceive(SWServer $server, int $fd, int $reactorID, string $data) : void
    {
        $c = $server->connection_info($fd);

        $this->events->notify(
            Events\Socket::RECEIVED,
            (new Connection)
                ->setID($fd)
                ->setReceived($data)
                ->setLocal($server->host, $c['server_port'])
                ->setRemote($c['remote_ip'], $c['remote_port'])
                ->from($this)
        );
    }

    /**
     * @param int $conn
     * @return bool
     */
    public function connected(int $conn = 0) : bool
    {
        return $this->server->exist($conn);
    }

    /**
     * @param string $data
     * @param int $conn
     * @return bool
     */
    public function send(string $data, int $conn = 0) : bool
    {
        return $this->connected($conn) ? $this->server->send($conn, $data) : false;
    }

    /**
     * @param int $conn
     * @return bool
     */
    public function close(int $conn = 0) : bool
    {
        return $this->server->close($conn) ? true : false;
    }
}
