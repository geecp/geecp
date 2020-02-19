<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// error_reporting(0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug' => true,
    // 应用Trace
    'app_trace' => false,
    // 应用模式状态
    'app_status' => '',
    // 是否支持多模块
    'app_multi_module' => true,
    // 入口自动绑定模块
    'auto_bind_module' => false,
    // 注册的根命名空间
    'root_namespace' => [],
    // 扩展函数文件
    'extra_file_list' => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type' => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler' => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler' => 'callback',
    // 默认时区
    'default_timezone' => 'PRC',
    // 是否开启多语言
    'lang_switch_on' => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter' => '',
    // 默认语言
    'default_lang' => 'zh-cn',
    // 应用类库后缀
    'class_suffix' => false,
    // 控制器类后缀
    'controller_suffix' => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module' => 'index',
    // 禁止访问模块
    'deny_module_list' => [],
    // 默认控制器名
    'default_controller' => 'Index',
    // 默认操作名
    'default_action' => 'index',
    // 默认验证器
    'default_validate' => '',
    // 默认的空控制器名
    'empty_controller' => 'Error',
    // 操作方法后缀
    'action_suffix' => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo' => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch' => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr' => '/',
    // URL伪静态后缀
    'url_html_suffix' => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param' => true,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type' => 0,
    // 是否开启路由
    'url_route_on' => true,
    // 路由使用完整匹配
    'route_complete_match' => true,
    // 路由配置文件（支持配置多个）
    'route_config_file' => ['route'],
    // 是否开启路由解析缓存
    'route_check_cache' => true,
    // 是否强制使用路由
    'url_route_must' => true,
    // 域名部署
    'url_domain_deploy' => false,
    // 域名根，如thinkphp.cn
    'url_domain_root' => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert' => true,
    // 默认的访问控制器层
    'url_controller_layer' => 'controller',
    // 表单请求类型伪装变量
    'var_method' => '_method',
    // 表单ajax伪装变量
    'var_ajax' => '_ajax',
    // 表单pjax伪装变量
    'var_pjax' => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache' => false,
    // 请求缓存有效期
    'request_cache_expire' => null,
    // 全局请求缓存排除规则
    'request_cache_except' => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Think',
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
        'auto_rule' => 1,
        // 模板路径
        'view_path' => '',
        // 模板后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end' => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str' => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl' => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle' => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type' => 'File',
        // 日志保存目录
        'path' => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache' => [
        // 驱动方式
        'type' => 'File',
        // 缓存保存目录
        'path' => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session' => [
        'id' => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix' => 'think',
        // 驱动方式 支持redis memcache memcached
        'type' => '',
        // 是否自动开启 SESSION
        'auto_start' => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie' => [
        // cookie 名称前缀
        'prefix' => '',
        // cookie 保存时间
        'expire' => 0,
        // cookie 保存路径
        'path' => '/',
        // cookie 有效域名
        'domain' => '',
        //  cookie 启用安全传输
        'secure' => false,
        // httponly设置
        'httponly' => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate' => [
        'type' => 'bootstrap',
        'var_page' => 'page',
        'list_rows' => 15,
    ],
    //插件扩展配置
    'addons' => [
        // 是否自动读取取插件钩子配置信息（默认是关闭）
        'autoload' => true,
        // 当关闭自动获取配置时需要手动配置hooks信息
        'hooks' => [
            // 可以定义多个钩子
            'testhook' => 'test', // 键为钩子名称，用于在业务中自定义钩子处理，值为实现该钩子的插件，
            // 多个插件可以用数组也可以用逗号分割
        ],
    ],
    /**
     * 支付宝
     */
    'alipay' =>[
      //商户ID
      'app_id' => '2018112862381219',
      //商户私钥
      'merchant_private_key' => 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDE9L03nDfEm/v3nrsIddelXRSr28+x+gDCVcy07Dcopfny0Rx/u8P5s8TPmzljBV4naYHDu/VWK/A3WX8Qll48dhVcNID/z1OSsLcibsLPDDh08h1BzH6xs7IAdwX0oFminkqm2s4BA/PDOi4ZmXJownbac0Lquf6uLALpIJ5xRvhlokoz5rZa2NqVC0mtgYRgVAT/jdBYTX8si/xj2Zuwd12/ZAmK5GPva4R/1FyZDkp1u8a6j3ECmoJviytMdiq9bf3xa5AdKA9gjTtPBfcPxzP8HOY9501m+5W5LRlLqN2eLvPpfozReVjB28YoI911xHJPwW1GoWBGZ6m5M/iXAgMBAAECggEAXLQRZ95oxWriEzAsOpQ090mzlBy3Sr8wokf+PV49rC1LU4YnktvPJ3X8+fbG8RuysHxRrs6GcJPn0jWWwDj2jCDMdwTjfwSYAqCY4mUd3pSS8kCPksvtCjlZXCypqfXbmtZErvqoIgQ5NTfqQPRfzH1Tsgi0g4UeId7nU9GdohUAsETnVbTg1J8YgDFm/d7+e6y97Xqa5JcIm3w4m9EsFgOCKL36hhksq7vGiefPZx1WGcjzSrEvZN7+10jY0q0KWweopqa9r2OJIzWgGr6LkUMAiJRCeUw6nqnGDbqBYW2yVocQeLISMCHgBIGkdzw7iEuLVI1EZKSBBi4GQ7v72QKBgQDwZCaXmyaJhb78Z89y5EZofMy2Ne0OvyWXYs95WSz0t18gkws/lyNVHv9eKPH1IQUfibk/Nu1083KQREA2a8vBXCKAmGOtaPxpe6KObobxFJXdVoFel8eSz1bmZJcXAOdDPcSwKYatADjQ5N8NZuU3fzYaDiFjr/Y5iP5mxvg/xQKBgQDRvpiwffFs5ZEh0/QiR67pHBwDUp+y/Yh/AjARFEUZCqRmVZVi2k20Dol1rCMqzbvTx/mftXTFU6DIO1h/6kTOceru4lfcR34xrUHSHJFDFIqhHhYQhSVl1ddaT6B2/lJU51Q48lBeJLkfYjbZKd9+XTS+SKQjO7hsmovsP5HgqwKBgB5A/HpoFFmKc5bgmcMj55iO3FSyRLtuYxTCYsoq5vWFfntjBi9inhfAZvM6w/jNxw9JDf9eslPdr7VoYYx31JTpO45jn2fbpqi/3p1+W41LwfCxSmbVQ1C+t3kXf8xtWK2lwHNLRp/PP1dK9qeBI6fwYqa00I7zPlHPY/UbZXLhAoGADMd5FB/ISuM90XrRgBkv/gH5bvZkxooN+/cNKK08fIcadyW1wV0dqN/N3j6Lsfrw+7lOaz2qDK/ItwpunPSQFqf4MW/W/JPai3pL7VpSO20P+TsVCxKhsa+yvEznsPhT2dpyVjnlTacdJF1ejCKx/Ef/g6x3W7IyzWMpLU6+O5sCgYEArUFhAEjstFpdMZuExCWGZRleieMMPcGeO0VOPB+dkwPH2fjnL99cAYzZC3MpSHtivqANS9iaBeLonni57cnQaPXIorAl3yrE8vWCGlliaBUaIbUTeU394/pwvV/4h2/ybBnmVSSG2CuFLD73uJRF2A87SaweuVSadqYxQA6TiGg=',
      //编码格式
      'charset' => 'UTF-8',
      //签名方式
      'sign_type'=>'RSA2',
      //支付宝网关
      'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
      //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
      'alipay_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAk7bpBS2ABFHSGi6ivAUWKk5I+kV2zOIYfy3OBolhm1K8jIw5KwsYfuo5nTFcI91x5HdzPrP46CFQe+yaG/vI7qfyZvU6IP3KGZ1MBdbTscYchoM7B8/lZDrtgzkzQWVHb/9TO/eP9IXAGkDnyvpD+QRbPI6a/jQpK6XvQvCYMdQ1nlHdL4eFhjBWGFuNWhUyWRrLqiFT2u9Eg+UuXbRhhVtTxFTpN2Pmy5esCs05migbSAMQWGpkVLAkfRPvC7A97bn+/NygYYEqA1K9YJL2MfTe/bd7XqI+HNYd1zlRNG/syPdUh3v0rx2bngdxop3YxBggjkU7JarMuestY+dM5QIDAQAB',
  ],


    'geecp'=>[
        //自动检测更新
        'checkupdate'         => false,


        //版本号
        'version' => '1.0.2',

        //API接口地址
        'api_url'             => 'https://ics.geecp.com',
    ],

];
