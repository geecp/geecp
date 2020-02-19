window.apiuri = '/';
//全局axios配置 
const instance = axios.create();
instance.defaults.baseURL = apiuri;
instance.defaults.headers.common['Authorization'] = Cookies.get('token') || '';
instance.defaults.params = {
    _: Math.round(new Date() / 1000)
}
instance.defaults.timeout = 6000 * 60 * 5;
instance.interceptors.request.use(
    config => {
        if (!!Cookies.get('token')) {
            config.headers.common['authorization'] = 'Bearer ' + Cookies.get('token');
        }
        return config;
    },
    error => {
        return Promise.reject(error)
    }
)
instance.interceptors.response.use(
    res => {
        if (res.data.code) {
            switch (res.data.code) {
                case 401:
                    Cookies.remove('token');
                    window.location.href = '/';
                    break;
            }
        }
        return res
    },
    err => {
        if (err && err.response) {
            const MESSAGE = err.response.data.message;
            switch (err.response.status) {
                case 400:
                    err.message = MESSAGE || '请求错误';
                    break;
                case 401:
                    err.message = '授权失败，请重新登录';
                    loginOut();
                    break;
                case 403:
                    err.message = MESSAGE || '拒绝访问';
                    break;
                case 404:
                    err.message = MESSAGE || `请求${err.response.config.url.split('/').pop().replace(/\.html/, '')}接口出错`;
                    break;
                case 408:
                    err.message = MESSAGE || '请求超时';
                    break;
                case 422:
                    err.message = MESSAGE || '数据格式或用户名密码错误';
                    break;
                case 500:
                    err.message = MESSAGE || '服务器内部错误';
                    break;
                case 501:
                    err.message = MESSAGE || '服务未实现';
                    break;
                case 502:
                    err.message = MESSAGE || '网关错误';
                    break;
                case 503:
                    err.message = MESSAGE || '服务不可用';
                    break;
                case 504:
                    err.message = MESSAGE || '网关超时';
                    break;
                case 505:
                    err.message = MESSAGE || 'HTTP版本不受支持';
                    break;
                default:
            }
        }
        alert(err.message);
        return Promise.reject(err)
    }
)

function ajax(url, params, type = 'get', files) {
    let config = { method: type || 'get' };
    if (typeof url == 'object') {
        instance.defaults.baseURL = url[0];
    } else {
        instance.defaults.baseURL = apiuri;
    }
    if (type !== 'post') {
        config.data = type ? {} : params;
        config.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
        config.transformRequest = [
            () => {
                let ret = new URLSearchParams();
                for (let key in params) {
                    ret.append(key, params[key]);
                }
                return ret;
            }
        ]
    } else {
        config.data = type ? {} : params;
        if (files) {
            config.headers = { 'Content-Type': 'multipart/form-data' };
            config.transformRequest = [
                () => {
                    if (!_.isPlainObject(params)) {
                        if (params instanceof FormData) {
                            return params;
                        } else {
                            let ret = new FormData(params);
                            return ret;
                        }
                    } else {
                        let ret = new FormData();
                        for (var key in params) {
                            ret.append(key, params[key]);
                        }
                        return ret;
                    }
                }
            ]
        } else {
            config.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
            config.transformRequest = [
                () => {
                    let ret = new URLSearchParams();
                    for (let key in params) {
                        ret.append(key, params[key]);
                    }
                    return ret;
                }
            ]
        }
    }
    return instance(url, config).then(response => {
        return response.data;
    });
}

const loginOut = () => {
    Cookies.remove('auth');
    location.href = 'login.html';
};

//查询对象子级 例:_comp.compName
const selectFn = (from, selector) => selector.split('.').reduce((prev, cur) => prev && prev[cur], from);
//form元素返回值
const getFormVal = (e) => Array.from(document.querySelectorAll(e)).reduce((a, b) => { a[b.name] = b.value; return a }, {});
//数组去重
const setArr = (ARR) => [...new Set([...ARR])];

//编辑器粘贴图片
const callEditor = () => {
        if (!!document.querySelector('[contenteditable="true"]')) {
            document.querySelector('[contenteditable="true"]').addEventListener('paste', function(e) {
                let cbd = e.clipboardData,
                    ua = window.navigator.userAgent,
                    t = $(this);
                // 如果是 Safari 直接 return	    
                if (!(e.clipboardData && e.clipboardData.items)) {
                    return;
                } // Mac平台下Chrome49版本以下 复制Finder中的文件的Bug Hack掉	    
                if (cbd.items && cbd.items.length === 2 && cbd.items[0].kind === "string" && cbd.items[1].kind === "file" && cbd.types && cbd.types.length === 2 && cbd.types[0] === "text/plain" && cbd.types[1] === "Files" && ua.match(/Macintosh/i) && Number(ua.match(/Chrome\/(\d{2})/i)[1]) < 49) {
                    return;
                }
                for (let i = 0; i < cbd.items.length; i++) {
                    let item = cbd.items[i];
                    if (item.kind == "file") {
                        let blob = item.getAsFile();
                        if (blob.size === 0) {
                            return;
                        }
                        let data = new FormData();
                        data.append('file', blob);
                        ajax('/uploadImg', data, 'post', true).then(e => {
                            let wrap = t,
                                file = e.data.toString(),
                                img = document.createElement("img");
                            img.src = file;
                            wrap.append(img);
                        });
                    }
                }
            }, false);
        }
    },
    /**
     * 获取url query参数
     * @param {String} param 需要获取的query参数
     */
    getQueryString = (param) => {
        const reg = new RegExp('(^|&)' + param + '=([^&]*)(&|$)');
        const r = window.location.search.substr(1).match(reg) || window.location.hash.substring((window.location.hash.search(/\?/)) + 1).match(reg);
        if (r != null) {
            return decodeURIComponent(r[2]);
        }
    },
    /**
     * @name 线转树
     * @param {Array}       ARR             需要处理的数组
     * @param {String}      keyName         作为分类的字段
     */
    Array2Object = (ARR, keyName) => {
        return ARR.reduce((a, b) => {
            const keys = b[keyName];
            if (a[keys] === undefined) { a[keys] = [] };
            a[keys].push(b);
            return a
        }, {})
    },
    /**
     * 文本转txt文件 下载
     * @param {String} filename 文件名
     * @param {Strign} text 文件内容
     */
    downloadTxt = (filename, text) => {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);

    },
    // 清理paramsFN()
    deleteUndefined = (object) => {
        for (let key in object) {
            delete object[(object[key] === undefined || object[key] === '') && key]
        }
        return object
    },
    /**
     * @name 字母升序
     * 
     * @param {String}      chars         字母 
     * @param {Number}      num           进几位
     * 
     */
    nextChars = (chars, num, type = 'lower') => {
        const char = chars.toLowerCase();
        const isChar = /^[a-zA-Z]*$/.test(char);
        const cx = char.charCodeAt(0);
        const CHARS = (!!isChar && cx + num < 123) ? String.fromCharCode(char.charCodeAt(0) + num) : false;
        return !!CHARS ? type === 'upper' ? CHARS.toUpperCase() : CHARS : 'Params Error'
    },
    /** 
     * @name 百度转二进制函数
     */
    convertCidrToBinary = (cidr) => {
        var array = cidr.split('/');
        var result = [];
        var valueArray = [];
        var index = cidr.indexOf(':');
        // 16进制
        if (index >= 0) {
            // ::只会出现一次
            valueArray = array[0].split('::');
            for (var i = 0; i < valueArray.length; i++) {
                var part = valueArray[i];
                var partTotal = '';
                if (part) {
                    var partArray = part.split(':');
                    for (var j = 0; j < partArray.length; j++) {
                        var numArray = partArray[j].split('');
                        var total = '';
                        for (var k = 0; k < numArray.length; k++) {
                            var num = parseInt(numArray[k], 16);
                            if (_u['default'].isNaN(num)) {
                                total += convertCidrToBinary(numArray[k]);
                            } else {
                                var binary = num.toString(2);
                                var pre = '';
                                for (var l = 0; l < 4 - binary.length; l++) {
                                    pre += '0';
                                }
                                total += pre + binary;
                            }
                        }
                        if (total.length < 16) {
                            var remain = '';
                            for (var l = 0; l < 16 - total.length; l++) {
                                remain += '0';
                            }
                            total = remain + total;
                        }
                        partTotal += total;
                    }
                }
                result.push(partTotal);
            }
            var totalLength = result.join('').length;
            var append = '';
            if (totalLength < 128) {
                for (var i = 0; i < 128 - totalLength; i++) {
                    append += '0';
                }
            }
            if (result.length > 1) {
                return result.join(append);
            }
            return index === 0 ? append + result : result + append;
        }
        // 10进制
        valueArray = array[0].split('.');
        for (var i = 0; i < valueArray.length; i++) {
            result.push(convertPartToBinary(valueArray[i]));
        }

        return result.join('');
    },
    convertPartToBinary = (part) => {
        part = +part;
        var binary = part.toString(2);
        var pre = '';

        for (var i = 0; i < 8 - binary.length; i++) {
            pre += '0';
        }

        return pre + binary;
    },
    /**
     * @name 返回时间格式
     * @param   {String}     time        接收格式2018-08-29T09:10:09.966Z
     */
    changeDatetime = (time) => {
        return moment(moment.utc(time).toDate()).format('YYYY-MM-DD HH:mm:ss');
    },
    /**
     * @name 返回百分比
     * @param   {Number}     num        需要计算的数值
     * @param   {Number}     total      总数
     */
    toPercent = (num, total) => {
        return (Math.round(num / total * 10000) / 100.00 + "%");
    }