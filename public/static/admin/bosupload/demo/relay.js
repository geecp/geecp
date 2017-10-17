/**
 * Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 *
 * @file relay.js
 * @author leeight
 */


var fs = require('fs');
var url = require('url');
var path = require('path');
var http = require('http');

var u = require('underscore');
var debug = require('debug')('proxy');

debug('Listening on port 8964');
http.createServer(function (req, res) {
    var parsedUrl = url.parse(req.url, true);
    if (parsedUrl.pathname === '/crossdomain.xml') {
        // Flash 会读取根目录下面的 crossdomain.xml 文件
        var abs = path.join(__dirname, 'crossdomain.xml');
        res.writeHead(200, {
            'content-type': 'text/xml',
            'content-length': fs.lstatSync(abs).size
        });
        fs.createReadStream(abs).pipe(res);
        return;
    }

    // 其它的请求代理到 bos 的外网服务器上面
    var query = parsedUrl.query;

    // 只允许 POST 来模拟HEAD 请求
    if (req.method !== 'POST' || query.httpMethod !== 'HEAD') {
        res.writeHead(403);
        res.end();
        return;
    }

    // 从 req.url 里面获取目标机器的地址
    var targetHost = req.url.split('/')[1];
    req.url = req.url.replace(/^\/[^\/]+/, '');
    debug('[%s] http://%s%s', req.method, targetHost, req.url);

    // 设置目标机器的 req.headers.Host
    req.headers.host = targetHost;

    var options = {
        host: targetHost,
        port: 80,
        path: req.url,
        // 按照BOS所需要的 Request Method 来代理
        method: query.httpMethod,
        headers: u.omit(req.headers, 'content-type', 'content-length')
    };
    debug('options = %j', options);
    var proxyReq = http.request(options, function (proxyRes) {
        var body = JSON.stringify({
            http_headers: proxyRes.headers,
            body: {}
        }, null, 2);
        proxyRes.headers['content-length'] = Buffer.byteLength(body);
        proxyRes.headers['content-type'] = 'application/json';
        res.writeHead(proxyRes.statusCode, proxyRes.headers);
        res.end(body);
    });
    proxyReq.on('error', function (e) {
        debug(e);
    });
    proxyReq.end();
}).listen(8964);









/* vim: set ts=4 sw=4 sts=4 tw=120: */
