# LeeDemo

first github project！@ 670554666@qq.com

IN -> Application ( Dispatcher (Request -> Router -> Controller/Action -> Response) ) -> OUT


---- 关于 Dispatcher

[yaf]
new Application -> bootstrap -> run( Dispatcher->dispatch )

其中 Dispatcher 对象下还挂有 Request 对象和钩子对象数组 pluginArray，bootstrap 时传入 Dispatcher 对象，这样可以在 bootstrap 中 Dispatcher->registerPlugin() 注册钩子对象，当 Dispatcher->dispatch() 时就可以执行到这些钩子对象中的挂钩点

但是此项目选择简化掉 Dispatcher，原因如下：

1、去掉了一个中间对象，这样 Request 和 pluginArray 直接挂到 Application 对象下

2、bootstrap 不需要传入 Dispatcher 对象，转而传入 Application 对象，通过 Application->registerPlugin() 注册钩子，并且额外的还可以访问 config 等属于 Applicaiotn 的属性（不必像 yaf 一样在 Dispatcher 里定义 getApplication，或者通过全局式的 Application::app()）


---- 关于 View

拒绝视图引擎，所以并没有实现如 yaf 的 ViewInterface，视图的解析由 Controller 完成（也仅是 extract 和 ob）


---- 关于对象传递

yaf 的 Request 对象从 Application 中创建出之后，传递给 Dispatcher，再通过 dispatch() 传递给 Router，并通过 handle() 传递给 Controller。并且 Dispatcher 在 Application 中被创建出之后还要传递给 Bootstrap。

但在这个项目里，Application 最先被创建出来，而 Request 挂在它的下面，理论上可以在任何地方获得这两个对象（不再需要作为参数传递）：Application::getInstance()->getRequestInstance()。
