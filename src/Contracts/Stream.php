<?php
/**
 * Socket conn API
 * User: moyo
 * Date: 06/08/2017
 * Time: 8:48 PM
 */

namespace Carno\Socket\Contracts;

use Carno\Net\Contracts\Conn;
use Carno\Net\Contracts\TCP;

interface Stream extends Conn
{
    /**
     * @param TCP $sock
     * @return Stream
     */
    public function from(TCP $sock) : Stream;

    /**
     * @return string
     */
    public function recv() : string;

    /**
     * @see TCP::send
     * @param string $data
     * @return bool
     */
    public function send(string $data) : bool;

    /**
     * @see TCP::write
     * @param string $data
     * @return bool
     */
    public function write(string $data) : bool;

    /**
     * @see TCP::close
     * @return bool
     */
    public function close() : bool;
}
