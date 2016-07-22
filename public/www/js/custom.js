/**
 * Created by 670554666@qq.com on 2016/7/22.
 */

function ajax(method, url, postData, callbackFunc, callbackEle) {
    var xhr = null;
    if (window.ActiveXObject) {
        xhr = new ActiveXObject('Microsoft.XMLHTTP') || new ActiveXObject('Msxml2.XMLHTTP');
    } else {
        xhr = new XMLHttpRequest();
    }
    xhr.open(method, url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                if (callbackFunc) {
                    callbackFunc(xhr.responseText, callbackEle);
                }
            } else {
                if (callbackFunc) {
                    callbackFunc(null, callbackEle);
                }
            }
        }
    };
    if (method.toLowerCase() === 'post') {

    }
}