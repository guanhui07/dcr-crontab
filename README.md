# dcr-swoole-crontab
A crontab component base on swoole.

## 声明
核心逻辑来自于 [Hyperf](https://hyperf.io) 的 [hyperf/crontab](https://github.com/hyperf/crontab) 组件。非常感谢 Hyperf 为大家提供这么优的组件。


## Requirement
- PHP >= 7.1
- [Composer](https://getcomposer.org/)

## Installation
`composer require  guanhui07/dcr_swoole_crontab`

## Documents
- [swoole](https://wiki.swoole.com/#/)
- [hyperf/crontab](https://hyperf.wiki/2.0/#/zh-cn/crontab?id=%e5%ae%9a%e6%97%b6%e4%bb%bb%e5%8a%a1)

## Usage

1. 在 `event.php` 里为 `swoole.init` 事件添加监听类。
```
return [
...
    'listem' => [
        ...
        'swoole.init' => [
            ...
            \DcrSwooleCrontab\Process\CrontabDispatcherProcess::class,
            ...
        ],
        ...
    ],
...
];

或 在启动脚本加
$crontab = new CrontabDispatcherProcess();
$crontab->handle();




```

2. 在配置文件 `crontab.php` 里添加 `crontab` 实例。
```
return [
    'crontab' => [
        (new \DcrSwooleCrontab\Crontab())->setName('test-1')
            ->setRule('* * * * * *')
            ->setCallback([Test::class, 'run'])
            ->setMemo('just a test crontab'),
        (new \DcrSwooleCrontab\Crontab())->setName('test-2')
            ->setRule('* * * * * *')
            ->setCallback([Test::class, 'run'])
            ->setMemo('just another test crontab'),
    ],
];
```

## License
MIT

## 我的其他包：
https://github.com/guanhui07/dcr  借鉴Laravel实现的 PHP Framework ，FPM模式、websocket使用的workerman、支持容器、PHP8特性attributes实现了路由注解、中间件注解、Laravel Orm等特性

https://github.com/guanhui07/redis Swoole模式下 Redis连接池

https://github.com/guanhui07/facade  facade、门面 fpm模式下可使用

https://github.com/guanhui07/dcr-swoole-crontab 基于swoole实现的crontab秒级定时任务

https://github.com/guanhui07/database  基于 illuminate/database 做的连接池用于适配Swoole的协程环境

https://github.com/guanhui07/dcr-swoole  高性能PHP Framework ，Cli模式，基于Swoole实现，常驻内存，协程框架，支持容器、切面、PHP8特性attributes实现了路由注解、中间件注解、支持Laravel Orm等特性


