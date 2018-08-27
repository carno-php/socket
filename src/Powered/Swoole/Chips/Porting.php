<?php
/**
 * Server porting
 * User: moyo
 * Date: 2018/8/27
 * Time: 10:49 AM
 */

namespace Carno\Socket\Powered\Swoole\Chips;

use Carno\Net\Events;
use Carno\Socket\Contracts\TServer;
use Carno\Socket\Options;
use Swoole\Server as SWServer;
use Swoole\Server\Port as SWPorted;

trait Porting
{
    /**
     * @param SWServer $master
     * @param SWPorted $ported
     * @param Events $events
     * @param Options $options
     * @return TServer
     */
    public function porting(SWServer $master, SWPorted $ported, Events $events, Options $options = null) : TServer
    {
        $this->server = $master;
        $this->events = $events;

        $this->registerEvs($ported, ['bufferFull', 'bufferEmpty']);

        $this->registerEvs($ported, $this->k2evs($events->keys(), [
            Events\Socket::CONNECTED => 'connect',
            Events\Socket::RECEIVED => 'receive',
            Events\Socket::CLOSED => 'close',
        ]));

        $options && $this->serverConfig($ported, $options->config());

        return $this;
    }

    /**
     * @param array $keys
     * @param array $map
     * @return array
     */
    private function k2evs(array $keys, array $map)
    {
        $evs = [];

        foreach ($keys as $key) {
            isset($map[$key]) && $evs[] = $map[$key];
        }

        return $evs;
    }
}
