<?php

declare(strict_types=1);
/**
 * 核心逻辑来自于 Hyperf 的 hyperf/crontab 组件.
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 *
 * @package think-swoole-crontab
 * @author OverNaive <overnaive20@gmail.com>
 */
namespace ThinkSwooleCrontab;

use think\Log;
use think\Config;

class CrontabRegister
{
    /**
     * @var CrontabManager
     */
    private $crontabManager;

    /**
     * @var Log
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * CrontabRegisterListener constructor.
     * @param CrontabManager $crontabManager
     */
    public function __construct(CrontabManager $crontabManager, Log $logger, Config $config)
    {
        $this->crontabManager = $crontabManager;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function handle(): void
    {
        $crontabs = $this->parseCrontabs();
        foreach ($crontabs as $crontab) {
            if ($crontab instanceof Crontab) {
                $this->logger->debug(sprintf('Crontab %s have been registered.', $crontab->getName()));
                $this->crontabManager->register($crontab);
            }
        }
    }

    private function parseCrontabs(): array
    {
        return $this->config->get('crontab.crontab', []);
    }
}
