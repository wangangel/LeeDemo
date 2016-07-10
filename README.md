# LeeDemo

first github project！@ 670554666@qq.com

IN -> Application ( Dispatcher (Request -> Router -> Controller/Action -> Response) ) -> OUT


#### 关于 Dispatcher
---

项目选择简化掉 yaf Dispatcher，原因如下：

1、去掉了一个中间对象，这样 Request 和 hookInstanceArray（原来的 pluginArray） 直接挂到 Application 对象下；

2、Bootstrap 不需要传入 Dispatcher 对象，而是直接 Application::getInstance()->registerHook(new xxxHook()) 即可注册钩子；

3、Router 直接 Application::getInstance()->getRequestInstance()->setControllerName() 解析路由并设置 controllerName 和 actionName，而不是像之前一样 Request 需要从 Application 到 Dispatcher 再“传递”到 Router，本质上参数传递对象也是引用，那 Request 挂在 Application 下，在这个前提之下，其它对象应该自己去获取句柄并操作（事实上 yaf 也有 Application::app()）；

4、Controller 中也可以通过 Application::getInstance()->getRequestInstance()->getGlobalQuery('postId') 获取 url 参数等（当然这么长可以封装到基类中）；


#### 关于 View
---
拒绝视图引擎，所以并没有实现如 yaf 的 ViewInterface，视图的解析由 Controller 完成（也仅是 extract 和 ob）
