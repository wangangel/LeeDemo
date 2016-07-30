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

// create element
function ce(tagName, attributes, innerHTML, appendChild) {
    var ele = document.createElement(tagName);
    if (attributes) {
        for (var key in attributes) {
            ele.setAttribute(key, attributes[key]);
        }
    }
    ele.innerHTML = innerHTML || '';
    if (appendChild) {
        ele.appendChild(appendChild);
    }
    return ele;
}

// class
function hasClass(ele, className) {
    return ele.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
}
function addClass(ele, className) {
    if (!this.hasClass(ele, className)) ele.className += ' ' + className;
}
function removeClass(ele, className) {
    if (hasClass(ele, className)){
        ele.className = ele.className.replace(new RegExp('(\\s|^)' + className + '(\\s|$)'), ' ');
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
                    callbackFunc(xhr.responseText, callbackEle || null);
                }
            } else {
                if (callbackFunc) {
                    callbackFunc(null, callbackEle || null);
                }
            }
        }
    };
    xhr.setRequestHeader('X-Requested-With', 'ajax');
    if (method.toLowerCase() === 'post') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    xhr.send(postData);
}

// post & +
function postEscape(data) {
    return data.replace(/\&/g, '%26').replace(/\+/g, '%2B');
}

// publish
var publish = {
    postAdd: function() {
        KindEditor.sync('#editor');
        ajax(
            'post',
            '/?c=publish&a=postAddSubmit',
            'title=' + postEscape($('#title').value) + '&categoryId=' + $('#category').value + '&body=' + postEscape($('#editor').innerHTML),
            function(data) {
                var json = eval('(' + data + ')');
                if (json.status === false) {
                    alert(json.code);
                } else {
                    alert('发布成功');
                }
            }
        );
    }
};

// access
var access = {
    registerMailSend: function(ele) {
        var email = $('#email');
        var captcha = $('#captcha');

        if (!/^([a-zA-Z0-9_\.\-]+)\@(qq|163)\.com$/.test(email.value)) {
            access._refreshCaptcha();
            form.showHelpBlock(email.parentNode, '邮箱未填写或格式有误');
            return false;
        } else {
            form.removeHelpBlock(email.parentNode);
        }

        if (!/^[a-z0-9]{5}$/.test(captcha.value)) {
            access._refreshCaptcha();
            form.showHelpBlock(captcha.parentNode.parentNode, '验证码未填写或格式有误');
            return false;
        } else {
            form.removeHelpBlock(captcha.parentNode.parentNode);
        }

        ele.setAttribute('disabled', 'disabled');
        ajax(
            'post',
            '/?c=access&a=registerMailSend',
            'email=' + postEscape(email.value) + '&captcha=' + postEscape(captcha.value),
            function(data) {
                var json = eval('(' + data + ')');
                if (json.status === false) {
                    access._refreshCaptcha();
                    alert(json.code);
                } else {
                    alert('发布成功');
                }
                access._refreshCaptcha();
            }
        );
    },
    _refreshCaptcha: function() {
        $('#captchaImage').setAttribute('src', '/?a=captcha&refresh' + Math.random());
    }
};

// form
var form = {
    showHelpBlock: function(ele, text) {
        var helpBlockEle = ce('span', {'class': 'help-block'}, text);
        if (ele.lastChild.nodeName.toLowerCase() !== 'span') {
            addClass(ele, 'has-error');
            ele.appendChild(helpBlockEle);
        }
        ele.focus();
    },
    removeHelpBlock: function(ele) {
        if (ele.lastChild.nodeName.toLowerCase() === 'span') {
            removeClass(ele, 'has-error');
            ele.removeChild(ele.lastChild);
        }
    }
};