/**
 * Created by 670554666@qq.com on 2016/7/22.
 */

// dom picker
function $(key) {
    if (key.substr(0, 1) === '#') {
        return document.getElementById(key.substr(1));
    } else if (key.substr(0, 1) === '.') {
        return document.getElementsByClassName(key.substr(1));
    } else {
        return document.getElementsByTagName(key);
    }
}

// ajax (sync as default)
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
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    xhr.send(postData);
}

// publish
var publish = {
    postAdd: function() {
        KindEditor.sync('#editor');
        var content = $('#editor').innerHTML;
        alert(content);
    }
};