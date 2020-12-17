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
namespace ThinkSwooleCrontab\Strategy;

use think\App;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var App
     */
    protected $app;

    /**
     * AbstractStrategy constructor.
     * @param $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }
}
