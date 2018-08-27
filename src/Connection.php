<?php
/**
 * Socket connection
 * User: moyo
 * Date: 04/08/2017
 * Time: 12:25 PM
 */

namespace Carno\Socket;

use Carno\Net\Connection as NET;
use Carno\Net\Contracts\TCP;
use Carno\Socket\Contracts\Stream;

class Connection extends NET implements Stream
{
    /**
     * @var TCP
     */
    private $sock = null;

    /**
     * @var string
     */
    private $data = null;

    /**
     * @param TCP $sock
     * @return Stream
     */
    public function from(TCP $sock) : Stream
    {
        $this->sock = $sock;
        return $this;
    }

    /**
     * @param string $data
     * @return self
     */
    public function setReceived(string $data) : self
    {
        $this->data = $data;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function recv() : string
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return bool
     */
    public function write(string $data) : bool
    {
        return $this->sock->write($data, $this->id);
    }

    /**
     * @param string $data
     * @return bool
     */
    public function send(string $data) : bool
    {
        return $this->sock->send($data, $this->id);
    }

    /**
     * @return bool
     */
    public function close() : bool
    {
        return $this->sock->close($this->id);
    }
}
