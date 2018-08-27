<?php
/**
 * Socket options
 * User: moyo
 * Date: 2018/7/31
 * Time: 3:10 PM
 */

namespace Carno\Socket;

class Options
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * Options constructor.
     * @param array ...$configs
     */
    public function __construct(array ...$configs)
    {
        foreach ($configs as $config) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * @return array
     */
    public function config() : array
    {
        return $this->config;
    }
}
