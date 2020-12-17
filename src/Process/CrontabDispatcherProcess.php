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
namespace ThinkSwooleCrontab\Process;

use Swoole\Timer;
use Swoole\Server;
use Swoole\Process;
use think\App;
use think\facade\Log;
use ThinkSwooleCrontab\CrontabRegister;
use ThinkSwooleCrontab\Scheduler;
use ThinkSwooleCrontab\Strategy\CoroutineStrategy;
use ThinkSwooleCrontab\Strategy\StrategyInterface;

class CrontabDispatcherProcess
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var CrontabRegister
     */
    private $crontabRegister;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var StrategyInterface
     */
    private $strategy;

    public function __construct(App $app)
    {
        $this->server = $app->get(Server::class);
        $this->crontabRegister = $app->make(CrontabRegister::class);
        $this->scheduler = $app->make(Scheduler::class);
        $this->strategy = $app->make(CoroutineStrategy::class);
    }

    public function handle(): void
    {
        $process = new Process(function (Process $process) {
            try {
                $this->crontabRegister->handle();

                while (true) {
                    $this->sleep();
                    $crontabs = $this->scheduler->schedule();
                    while (! $crontabs->isEmpty()) {
                        $crontab = $crontabs->dequeue();
                        $this->strategy->dispatch($crontab);
                    }
                }
            } catch (\Throwable $throwable) {
                Log::error($throwable->getMessage());
            } finally {
                Timer::clearAll();
                sleep(5);
            }
        }, false, 0, true);

        $this->server->addProcess($process);
    }

    private function sleep()
    {
        $current = date('s', time());
        $sleep = 60 - $current;
        Log::debug('Crontab dispatcher sleep ' . $sleep . 's.');
        $sleep > 0 && \Swoole\Coroutine::sleep($sleep);
    }
}
