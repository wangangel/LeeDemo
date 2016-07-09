# LeeDemo

first github project！@ 670554666@qq.com

IN -> Application ( Dispatcher (Request -> Router -> Controller/Action -> Response) ) -> OUT



关于 Dispatcher：

[yaf]
new Application -> bootstrap -> run( Dispatcher->dispatch )

其中 Dispatcher 对象下还挂有 Request 对象和插件对象数组 pluginArray，bootstrap 时传入 Dispatcher 对象，这样可以在 bootstrap 中 registerPlugin，这样 Dispatcher->dispatch 可以执行到这些钩子（事实上 yaf 的 plugin 从意义上讲算是钩子？）

但是此项目选择简化掉 Dispatcher，原因如下：

1、去掉了一个中间对象，这样 Request 和 pluginArray 直接挂到 Application 下
2、bootstrap 不需要传入 Dispatcher 对象，转而传入 Application 对象，registerPlugin 由 Application 完成，并且额外的还可以访问 config 等属于 Applicaiotn 的属性（不必像 yaf 一样在 Dispatcher 里定义 getApplication，或者通过全局式的 Application::app()）
