<?php
/**
 * Buffer manager
 * User: moyo
 * Date: 2018/8/2
 * Time: 11:24 AM
 */

namespace Carno\Socket\Powered\Swoole\Chips;

use Carno\Net\Contracts\TCP;
use Carno\Promise\Promise;
use Carno\Promise\Promised;
use Closure;

trait Buffered
{
    /**
     * @var bool[]
     */
    private $writable = [];

    /**
     * @var Promised[]
     */
    private $waits = [];

    /**
     * @param object $sw
     * @param int $conn
     */
    public function evBufferFull(object $sw, int $conn = 0) : void
    {
        $this->writable[$conn] = false;
    }

    /**
     * @param object $sw
     * @param int $conn
     */
    public function evBufferEmpty(object $sw, int $conn = 0) : void
    {
        $this->writable[$conn] = true;
        if (isset($this->waits[$conn])) {
            $wait = $this->waits[$conn];
            unset($this->waits[$conn]);
            $wait->resolve($this, $conn);
        }
    }

    /**
     * @param Closure $do
     * @param int $conn
     * @return bool
     */
    public function ifWritable(Closure $do, int $conn = 0) : bool
    {
        if (!$this->connected($conn)) {
            return false;
        }

        if ($this->writable[$conn] ?? true) {
            $do($this, $conn);
            return true;
        }

        ($this->waits[$conn] ?? $this->waits[$conn] = Promise::deferred())
            ->then(function (object $sock, int $conn) use ($do) {
                /**
                 * @var static $sock
                 */
                $sock->ifWritable($do, $conn);
            })
        ;

        return false;
    }

    /**
     * @see TCP::write
     * @param string $data
     * @param int $conn
     * @return bool
     */
    public function write(string $data, int $conn = 0) : bool
    {
        return $this->ifWritable(static function (TCP $sock, int $conn) use ($data) {
            return $sock->send($data, $conn);
        }, $conn);
    }
}
