# LeeDemo

first github project！@ 670554666@qq.com

####执行流程（伪代码）
---
1、Application->bootstrap() => Bootstrap->_initXXX() => Application->registerHook(new xxxHook())

2、Application->run()

3、Router->route() => Request->setControllerName()->setActionName()

4、$ret = Controller->action()

5、$ret = View->render($ret)

6、Response->setBody($ret)

7、Response->response()
