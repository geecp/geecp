window.apiuri = '/';
//全局axios配置 
const instance = axios.create();
instance.defaults.baseURL = apiuri;
instance.defaults.headers.common['Authorization'] = Cookies.get('token') || '';
instance.defaults.params = {
    _: Math.round(new Date() / 1000)
}
instance.defaults.timeout = 20000;
instance.interceptors.request.use(
    config => {
        if (!!Cookies.get('token')) {
            config.headers.common['authorization'] = 'Bearer ' + Cookies.get('token');
        }
        return config
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
                    err.message = MESSAGE || '请求错误'
                    break
                case 401:
                    err.message = '授权失败，请重新登录';
                    loginOut();
                    break
                case 403:
                    err.message = MESSAGE || '拒绝访问'
                    break
                case 404:
                    err.message = MESSAGE || `请求${err.response.config.url.split('/').pop().replace(/\.html/, '')}接口出错`
                    break
                case 408:
                    err.message = MESSAGE || '请求超时'
                    break
                case 422:
                    err.message = MESSAGE || '数据格式或用户名密码错误'
                    break
                case 500:
                    err.message = MESSAGE || '服务器内部错误'
                    break
                case 501:
                    err.message = MESSAGE || '服务未实现'
                    break
                case 502:
                    err.message = MESSAGE || '网关错误'
                    break
                case 503:
                    err.message = MESSAGE || '服务不可用'
                    break
                case 504:
                    err.message = MESSAGE || '网关超时'
                    break
                case 505:
                    err.message = MESSAGE || 'HTTP版本不受支持'
                    break
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