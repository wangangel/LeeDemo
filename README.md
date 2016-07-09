# LeeDemo

first github project！@ 670554666@qq.com

IN -> Application ( Dispatcher (Request -> Router -> Controller/Action -> Response) ) -> OUT



---- 关于 Dispatcher

[yaf]
new Application -> bootstrap -> run( Dispatcher->dispatch )

其中 Dispatcher 对象下还挂有 Request 对象和钩子对象数组 pluginArray，bootstrap 时传入 Dispatcher 对象，这样可以在 bootstrap 中 Dispatcher->registerPlugin() 注册钩子对象，当 Dispatcher->dispatch() 时就可以执行到这些钩子对象中的挂钩点

但是此项目选择简化掉 Dispatcher，原因如下：

1、去掉了一个中间对象，这样 Request 和 pluginArray 直接挂到 Application 下
2、bootstrap 不需要传入 Dispatcher 对象，转而传入 Application 对象，通过 Application->registerPlugin() 注册钩子，并且额外的还可以访问 config 等属于 Applicaiotn 的属性（不必像 yaf 一样在 Dispatcher 里定义 getApplication，或者通过全局式的 Application::app()）
