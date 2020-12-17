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

use Carbon\Carbon;
use Swoole\Timer;
use Swoole\Coroutine;
use think\App;
use think\Log;
use ThinkSwooleCrontab\Crontab;

class Executor
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Log
     */
    private $logger;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->logger = $app->log;
    }

    public function execute(Crontab $crontab)
    {
        if (! $crontab instanceof Crontab || ! $crontab->getExecuteTime()) {
            return;
        }
        $diff = $crontab->getExecuteTime()->diffInRealSeconds(new Carbon());
        $callback = null;
        switch ($crontab->getType()) {
            case 'callback':
                [$class, $method] = $crontab->getCallback();
                $parameters = $crontab->getCallback()[2] ?? null;
                if ($class && $method && class_exists($class) && method_exists($class, $method)) {
                    $callback = function () use ($class, $method, $parameters, $crontab) {
                        $runnable = function () use ($class, $method, $parameters, $crontab) {
                            try {
                                $result = true;
                                $instance = $this->app->make($class);
                                if ($parameters && is_array($parameters)) {
                                    $instance->{$method}(...$parameters);
                                } else {
                                    $instance->{$method}();
                                }
                            } catch (\Throwable $throwable) {
                                $result = false;
                            } finally {
                                $this->logResult($crontab, $result);
                            }
                        };

                        Coroutine::create($runnable);
                    };
                }
                break;
        }
        $callback && Timer::after($diff > 0 ? $diff * 1000 : 1, $callback);
    }

    protected function logResult(Crontab $crontab, bool $isSuccess)
    {
        if ($isSuccess) {
            $this->logger->info(sprintf('Crontab task [%s] executed successfully at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
        } else {
            $this->logger->error(sprintf('Crontab task [%s] failed execution at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
        }
    }
}
