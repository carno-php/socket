<?php
/**
 * Swoole client
 * User: moyo
 * Date: 27/09/2017
 * Time: 4:57 PM
 */

namespace Carno\Socket\Powered\Swoole;

use Carno\Net\Address;
use Carno\Net\Events;
use Carno\Socket\Connection;
use Carno\Socket\Contracts\TClient;
use Carno\Socket\Options;
use Carno\Socket\Powered\Swoole\Chips\Buffered;
use Swoole\Client as SWClient;

class Client implements TClient
{
    use Buffered;

    /**
     * @var array
     */
    private $acceptEvs = [
        'bufferFull', 'bufferEmpty',
        'connect', 'close',
        'receive',
        'error',
    ];

    /**
     * @var Address
     */
    private $address = null;

    /**
     * @var Events
     */
    private $events = null;

    /**
     * @var SWClient
     */
    private $client = null;

    /**
     * @param Address $address
     * @param Events $events
     * @param Options $options
     * @return TClient
     */
    public function connect(Address $address, Events $events, Options $options = null) : TClient
    {
        $this->address = $address;
        $this->events = $events;

        $this->client = new SWClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $options && $this->client->set($options->config());

        foreach ($this->acceptEvs as $ev) {
            $this->client->on($ev, [$this, sprintf('ev%s', ucfirst($ev))]);
        }

        $this->client->connect($address->host(), $address->port());

        return $this;
    }

    /**
     * @param SWClient $client
     */
    public function evConnect(SWClient $client) : void
    {
        $local = $client->getsockname();

        $this->events->notify(
            Events\Socket::CONNECTED,
            (new Connection)
                ->setLocal($local['host'], $local['port'])
                ->setRemote($this->address->host(), $this->address->port())
                ->from($this)
        );
    }

    /**
     * @param SWClient $client
     */
    public function evClose(SWClient $client) : void
    {
        $this->events->notify(
            Events\Socket::CLOSED,
            (new Connection)
                ->setRemote($this->address->host(), $this->address->port())
                ->from($this)
        );
    }

    /**
     * @param SWClient $client
     */
    public function evError(SWClient $client) : void
    {
        $this->events->notify(
            Events\Socket::ERROR,
            (new Connection)
                ->setRemote($this->address->host(), $this->address->port())
                ->from($this)
        );
    }

    /**
     * @param SWClient $client
     * @param string $data
     */
    public function evReceive(SWClient $client, string $data) : void
    {
        $this->events->notify(
            Events\Socket::RECEIVED,
            (new Connection)
                ->setReceived($data)
                ->setRemote($this->address->host(), $this->address->port())
                ->from($this)
        );
    }

    /**
     * @param int $conn
     * @return bool
     */
    public function connected(int $conn = 0) : bool
    {
        return $this->client->isConnected();
    }

    /**
     * @param string $data
     * @param int $conn
     * @return bool
     */
    public function send(string $data, int $conn = 0) : bool
    {
        return $this->connected($conn) ? !! $this->client->send($data) : false;
    }

    /**
     * @param int $conn
     * @return bool
     */
    public function close(int $conn = 0) : bool
    {
        return $this->connected($conn) ? $this->client->close() : false;
    }
}
