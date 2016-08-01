/**
 * Created by 670554666@qq.com on 2016/7/22.
 */


// JS
var JS = function(selector, context) {

};
JS.prototype = {
    construct: JS
};
window.$ = function(selector, context) {
    return new JS(selector, context);
};


// validate
JS.prototype.validate = function() {
    console.log('validate');
};