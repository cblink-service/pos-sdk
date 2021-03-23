<?php

/*
 * This file is part of the cblinkservice//pos-sdk.
 *
 * (c) jinjun <757258777@qq.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace CblinkService\PosSdk;

use CblinkService\PosSdk\Contracts\GatewayInterface;
use CblinkService\PosSdk\Support\Config;

class PosApi
{
    protected $config;

    protected $defaultGateway;

    protected $gateways;

    public function __construct(array $config = [])
    {
        $this->config = new Config($config);

        if (!empty($this->config->get('driver'))) {
            $this->setDefaultGateway($this->config->get('driver'));
        }
    }

    /**
     * 默认网关.
     *
     * @param $driver
     *
     * @return $this'
     */
    public function setDefaultGateway($driver)
    {
        $this->defaultGateway = $driver;

        return $this;
    }

    /**
     * 获取默认网关.
     *
     * @return mixed
     */
    public function getDefaultGateway()
    {
        if (empty($this->defaultGateway)) {
            throw new \RuntimeException('No default gateway configured.');
        }

        return $this->defaultGateway;
    }

    /**
     * 网关传入.
     *
     * @param $driver
     *
     * @return mixed
     */
    public function gateway($driver)
    {
        $name = $driver ?: $this->getDefaultGateway();

        if (!isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($driver);
        }

        return $this->gateways[$name];
    }

    /**
     * 创建网关类.
     *
     * @param $driver
     *
     * @return mixed
     */
    protected function createGateway($driver)
    {
        $className = $this->formatGatewayClassName($driver);

        $config = $this->config->get("channels.{$driver}", []);

        return $this->makeGateway($className, $config);
    }

    /**
     * 获取网关类.
     *
     * @param $driver
     *
     * @return string
     */
    public function formatGatewayClassName($driver)
    {
        if (\class_exists($driver) && \in_array(GatewayInterface::class, \class_implements($driver))) {
            return $driver;
        }

        $driver = \ucfirst(\str_replace(['-', '_', ''], '', $driver));

        return __NAMESPACE__."\\Gateways\\{$driver}";
    }

    /**
     * 实例化对应网关类型.
     *
     * @param $gateway
     * @param $config
     *
     * @return mixed
     */
    protected function makeGateway($gateway, $config)
    {
        // !\in_array(GatewayInterface::class, \class_implements($gateway))
        if (!\class_exists($gateway)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" is a invalid pos gateway.', $gateway));
        }

        return new $gateway($config);
    }
}
