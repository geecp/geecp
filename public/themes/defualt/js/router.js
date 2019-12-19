const Router = function(config) {
        config = config || {};
        // 页面容器
        let app = document.getElementById(config.el) || document.body
        let routes = Object.prototype.toString.call(config.routes) === '[object Array]' ?
            config.routes : [];
        // 加载页面
        let load = function(route) {
            if (route) {
                try {
                    let beforeLoad = route.beforeLoad || function() {}
                    let afterLoad = route.afterLoad || function() {}
                    beforeLoad()
                    if (!!route.url) {
                        let thtml = '';
                        if (route.url == '/') {
                            route.url = '/home';
                        }
                        if (route.url.indexOf('~') > 0) {
                            route.url = route.url.split('~')[0];
                        }
                        axios(window.BASICPRO + route.url + '.html').then(e => {
                            if (!!e.data) {
                                thtml = e.data;
                            } else {
                                thtml = TEMPS.emptyPro(route.url.split('/')[2]);
                            }
                            // $('#warp').attr('data-url', route.url);
                            $(app).html(thtml);
                            window.MARKER = [''];
                        });
                    }
                    afterLoad()
                } catch (e) {
                    console.warn(e.message);
                }
            }
        };
        // 根据 location 的 hash 属性实现页面切换
        let redirect = function(event) {
            let url = window.location.hash.slice(1).indexOf('?') ? window.location.hash.slice(1).split('?')[0] : window.location.hash.slice(1) || '/';
            url = url.slice(1).indexOf('~') ? url.slice(1).split('~')[0] : url;
            url = '/' + url;
            for (let route of routes) {
                if (url === route.url) {
                    load(route)
                    return
                }
            }
            load(routes[0])
        };
        // 添加路由规则
        this.push = function(route) {
            if (Object.prototype.toString.call(route) === '[object Object]') {
                routes.push(route)
            }
        };
        // 更改页面容器
        this.bind = function(el) {
            app = document.getElementById(el) || document.body
        };
        // event
        window.addEventListener('load', redirect, false);
        // 监控 hash 变化
        window.addEventListener('hashchange', redirect, false);
    },
    getTemplate = async(url) => {
        try {
            return axios.get(url + '.html').then(e => {
                return e.data;
            })
        } catch (e) {
            console.log('您的浏览器暂不兼容');
        }
    };

// 创建路由对象 
let router = new Router()
router.bind('warp');
router.push({ url: '/' });
//路由
//iam 个人中心相关路由
router.push({ url: '/iam/index' }); //个人中心首页