# LeeDemo

first github project！@ 670554666@qq.com

IN -> Application ( Dispatcher (Request -> Router -> Controller/Action -> Response) ) -> OUT


#### 关于 Dispatcher
---
项目选择简化掉 yaf Dispatcher，原因如下：
1、去掉了一个中间对象，这样 Request 和 hookInstanceArray（原来的 pluginArray） 直接挂到 Application 对象下；
2、bootstrap 不需要传入 Dispatcher 对象，而是直接 Application::getInstance()->registerHook(new xxxHook()) 即可注册钩子；


#### 关于 View
---
拒绝视图引擎，所以并没有实现如 yaf 的 ViewInterface，视图的解析由 Controller 完成（也仅是 extract 和 ob）


#### 关于对象传递
---
yaf 的 Request 对象从 Application 中创建出之后，传递给 Dispatcher，再通过 dispatch() 传递给 Router，并通过 handle() 传递给 Controller。并且 Dispatcher 在 Application 中被创建出之后还要传递给 Bootstrap。

但在这个项目里，Dispatcher 已经废弃，Request 挂在 Application 下面，理论上可以在任何地方获得这两个对象（不再需要作为参数传递）：Application::getInstance()->getRequestInstance()。
