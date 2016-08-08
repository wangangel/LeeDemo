/**
 * Created by 670554666@qq.com on 2016/7/22.
 */

// create element
function ce(tagName, attributes, innerHTML) {
    var ele = document.createElement(tagName);
    for (var key in attributes) {
        ele.setAttribute(key, attributes[key]);
    }
    ele.innerHTML = innerHTML || '';
    return ele;
}

// class
function hasClass(ele, cls) {
    return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
}
function addClass(ele, cls) {
    if (!hasClass(ele, cls)) {
        ele.className += ' ' + cls;
    }
}
function removeClass(ele, cls) {
    if (hasClass(ele, cls)) {
        ele.className = ele.className.replace(new RegExp('(\\s|^)' + cls + '(\\s|$)'), ' ');
    }
}

// validate
var validate = {
    valid: function(ele) {
        var subs = ele.getElementsByTagName('*');
        for (var i = 0; i < subs.length; i++) {
            var sub = subs[i];
            if (sub.hasAttribute('validate')) {
                // required
                if (sub.hasAttribute('validate-required') && sub.getAttribute('validate-required').toLowerCase() === 'yes') {
                    if (sub.value === '' || /^\s+$/.test(sub.value)) {
                        this._showInputError(sub, sub.getAttribute('validate-required-message'));
                        return false;
                    }
                }
                // pattern
                if (sub.hasAttribute('validate-pattern')) {
                    if (!(sub.getAttribute('validate-pattern')).test(sub.value)) {
                        this._showInputError(sub, sub.getAttribute('validate-pattern-message'));
                        return false;
                    }
                }
                this._removeInputError(sub);
            }
        }
    },
    _showInputError: function(ele, message) {
        addClass(ele.parentNode, 'has-error');
        if (ele.parentNode.lastChild.nodeName.toLowerCase() === 'span') {
            ele.parentNode.removeChild(ele.parentNode.lastChild);
        }
        ele.parentNode.appendChild(
            ce('span', {'class': 'help-block'}, message)
        );
        ele.focus();
    },
    _removeInputError: function(ele) {
        removeClass(ele.parentNode, 'has-error');
        if (ele.parentNode.lastChild.nodeName.toLowerCase() === 'span') {
            ele.parentNode.removeChild(ele.parentNode.lastChild);
        }
    }
};