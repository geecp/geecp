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
 * @file utils.js
 * @author leeight
 */

var urlModule = require('url');
var qsModule = require('querystring');

var u = require('underscore');
var sdk = require('bce-sdk-js');
var SparkMD5 = require('spark-md5');

var Queue = require('./queue');

/**
 * 把文件进行切片，返回切片之后的数组
 *
 * @param {Blob} file 需要切片的大文件.
 * @param {string} uploadId 从服务获取的uploadId.
 * @param {number} chunkSize 分片的大小.
 * @param {string} bucket Bucket Name.
 * @param {string} object Object Name.
 * @return {Array.<Object>}
 */
exports.getTasks = function (file, uploadId, chunkSize, bucket, object) {
    var leftSize = file.size;
    var offset = 0;
    var partNumber = 1;

    var tasks = [];

    while (leftSize > 0) {
        var partSize = Math.min(leftSize, chunkSize);

        tasks.push({
            file: file,
            uploadId: uploadId,
            bucket: bucket,
            object: object,
            partNumber: partNumber,
            partSize: partSize,
            start: offset,
            stop: offset + partSize - 1
        });

        leftSize -= partSize;
        offset += partSize;
        partNumber += 1;
    }

    return tasks;
};

exports.getAppendableTasks = function (fileSize, offset, chunkSize) {
    var leftSize = fileSize - offset;
    var tasks = [];

    while (leftSize) {
        var partSize = Math.min(leftSize, chunkSize);
        tasks.push({
            partSize: partSize,
            start: offset,
            stop: offset + partSize - 1
        });

        leftSize -= partSize;
        offset += partSize;
    }
    return tasks;
};

exports.parseSize = function (size) {
    if (typeof size === 'number') {
        return size;
    }

    // mb MB Mb M
    // kb KB kb k
    // 100
    var pattern = /^([\d\.]+)([mkg]?b?)$/i;
    var match = pattern.exec(size);
    if (!match) {
        return 0;
    }

    var $1 = match[1];
    var $2 = match[2];
    if (/^k/i.test($2)) {
        return $1 * 1024;
    }
    else if (/^m/i.test($2)) {
        return $1 * 1024 * 1024;
    }
    else if (/^g/i.test($2)) {
        return $1 * 1024 * 1024 * 1024;
    }
    return +$1;
};

/**
 * 判断一下浏览器是否支持 xhr2 特性，如果不支持，就 fallback 到 PostObject
 * 来上传文件
 *
 * @return {boolean}
 */
exports.isXhr2Supported = function () {
    // https://github.com/Modernizr/Modernizr/blob/f839e2579da2c6331eaad922ae5cd691aac7ab62/feature-detects/network/xhr2.js
    return 'XMLHttpRequest' in window && 'withCredentials' in new XMLHttpRequest();
};

exports.isAppendable = function (headers) {
    return headers['x-bce-object-type'] === 'Appendable';
};

/**
 * 规范化用户的输入
 *
 * @param {string} endpoint The endpoint will be normalized
 * @return {string}
 */
exports.normalizeEndpoint = function (endpoint) {
    return endpoint.replace(/(\/+)$/, '');
};

exports.getDefaultACL = function (bucket) {
    return {
        accessControlList: [
            {
                service: 'bce:bos',
                region: '*',
                effect: 'Allow',
                resource: [bucket + '/*'],
                permission: ['READ', 'WRITE']
            }
        ]
    };
};

/**
 * 生成uuid
 *
 * @return {string}
 */
exports.uuid = function () {
    var random = (Math.random() * Math.pow(2, 32)).toString(36);
    var timestamp = new Date().getTime();
    return 'u-' + timestamp + '-' + random;
};

/**
 * 生成本地 localStorage 中的key，来存储 uploadId
 * localStorage[key] = uploadId
 *
 * @param {Object} option 一些可以用来计算key的参数.
 * @param {string} generator 内置的只有 default 和 md5
 * @return {Promise}
 */
exports.generateLocalKey = function (option, generator) {
    if (generator === 'default') {
        return sdk.Q.resolve([
            option.blob.name, option.blob.size,
            option.chunkSize, option.bucket,
            option.object
        ].join('&'));
    }
    else if (generator === 'md5') {
        return exports.md5sum(option.blob).then(function (md5) {
            return [
                md5,
                option.blob.name,
                option.blob.size,
                option.chunkSize,
                option.bucket,
                option.object
            ].join('&');
        });
    }
    return sdk.Q.resolve(null);
};

/**
 * 基于 SparkMD5 来快速的计算 blob 的md5
 * 貌似直接使用 bce-sdk-js/src/crypto 里面的 md5blob，对于 300M 的文件，Chrome 直接
 * 挂掉了
 *
 * @param {Blob} file 需要计算md5的文件内容.
 * @return {Promise}
 */
exports.md5sum = function (file) {
    var blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice;
    var chunkSize = 2097152;
    var chunks = Math.ceil(file.size / chunkSize);
    var currentChunk = 0;
    var spark = new SparkMD5.ArrayBuffer();
    var fileReader = new FileReader();

    var deferred = sdk.Q.defer();

    fileReader.onload = function (e) {
        spark.append(e.target.result);
        currentChunk++;

        if (currentChunk < chunks) {
            loadNext();
        }
        else {
            deferred.resolve(spark.end());
        }
    };
    fileReader.onerror = function (error) {
        deferred.reject(error);
    };

    function loadNext() {
        var start = currentChunk * chunkSize;
        var end = ((start + chunkSize) >= file.size) ? file.size : start + chunkSize;
        fileReader.readAsArrayBuffer(blobSlice.call(file, start, end));
    }
    loadNext();

    return deferred.promise;
};

exports.getDefaultPolicy = function (bucket) {
    if (bucket == null) {
        return null;
    }

    var now = new Date().getTime();

    // 默认是 24小时 之后到期
    var expiration = new Date(now + 24 * 60 * 60 * 1000);
    var utcDateTime = expiration.toISOString().replace(/\.\d+Z$/, 'Z');

    return {
        expiration: utcDateTime,
        conditions: [
            {bucket: bucket}
        ]
    };
};

/**
 * 根据key获取localStorage中的uploadId
 *
 * @param {string} key 需要查询的key
 * @return {string}
 */
exports.getUploadId = function (key) {
    return localStorage.getItem(key);
};


/**
 * 根据key设置localStorage中的uploadId
 *
 * @param {string} key 需要查询的key
 * @param {string} uploadId 需要设置的uploadId
 */
exports.setUploadId = function (key, uploadId) {
    localStorage.setItem(key, uploadId);
};

/**
 * 根据key删除localStorage中的uploadId
 *
 * @param {string} key 需要查询的key
 */
exports.removeUploadId = function (key) {
    localStorage.removeItem(key);
};

/**
 * 取得已上传分块的etag
 *
 * @param {number} partNumber 分片序号.
 * @param {Array} existParts 已上传完成的分片信息.
 * @return {string} 指定分片的etag
 */
function getPartEtag(partNumber, existParts) {
    var matchParts = u.filter(existParts || [], function (part) {
        return +part.partNumber === partNumber;
    });
    return matchParts.length ? matchParts[0].eTag : null;
}

/**
 * 因为 listParts 会返回 partNumber 和 etag 的对应关系
 * 所以 listParts 返回的结果，给 tasks 中合适的元素设置 etag 属性，上传
 * 的时候就可以跳过这些 part
 *
 * @param {Array.<Object>} tasks 本地切分好的任务.
 * @param {Array.<Object>} parts 服务端返回的已经上传的parts.
 */
exports.filterTasks = function (tasks, parts) {
    u.each(tasks, function (task) {
        var partNumber = task.partNumber;
        var etag = getPartEtag(partNumber, parts);
        if (etag) {
            task.etag = etag;
        }
    });
};

/**
 * 把用户输入的配置转化成 html5 和 flash 可以接收的内容.
 *
 * @param {string|Array} accept 支持数组和字符串的配置
 * @return {string}
 */
exports.expandAccept = function (accept) {
    var exts = [];

    if (u.isArray(accept)) {
        // Flash要求的格式
        u.each(accept, function (item) {
            if (item.extensions) {
                exts.push.apply(exts, item.extensions.split(','));
            }
        });
    }
    else if (u.isString(accept)) {
        exts = accept.split(',');
    }

    // 为了保证兼容性，把 mimeTypes 和 exts 都返回回去
    exts = u.map(exts, function (ext) {
        return /^\./.test(ext) ? ext : ('.' + ext);
    });

    return exts.join(',');
};

exports.extToMimeType = function (exts) {
    var mimeTypes = u.uniq(u.map(exts.split(','), function (ext) {
        if (ext.indexOf('/') !== -1) {
            return ext;
        }
        return sdk.MimeType.guess(ext);
    }));

    return mimeTypes.join(',');
};

exports.expandAcceptToArray = function (accept) {
    if (!accept || u.isArray(accept)) {
        return accept;
    }

    if (u.isString(accept)) {
        return [
            {title: 'All files', extensions: accept}
        ];
    }

    return [];
};

/**
 * 转化一下 bos url 的格式
 * http://bj.bcebos.com/v1/${bucket}/${object} -> http://${bucket}.bj.bcebos.com/v1/${object}
 *
 * @param {string} url 需要转化的URL.
 * @return {string}
 */
exports.transformUrl = function (url) {
    var pattern = /(https?:)\/\/([^\/]+)\/([^\/]+)\/([^\/]+)/;
    return url.replace(pattern, function (_, protocol, host, $3, $4) {
        if (/^v\d$/.test($3)) {
            // /v1/${bucket}/...
            return protocol + '//' + $4 + '.' + host + '/' + $3;
        }
        // /${bucket}/...
        return protocol + '//' + $3 + '.' + host + '/' + $4;
    });
};

exports.isBlob = function (body) {
    var blobCtor = null;

    if (typeof Blob !== 'undefined') {
        // Chrome Blob === 'function'
        // Safari Blob === 'undefined'
        blobCtor = Blob;
    }
    else if (typeof mOxie !== 'undefined' && u.isFunction(mOxie.Blob)) {
        blobCtor = mOxie.Blob;
    }
    else {
        return false;
    }

    return body instanceof blobCtor;
};

exports.now = function () {
    return new Date().getTime();
};

exports.toDHMS = function (seconds) {
    var days = 0;
    var hours = 0;
    var minutes = 0;

    if (seconds >= 60) {
        minutes = ~~(seconds / 60);
        seconds = seconds - minutes * 60;
    }

    if (minutes >= 60) {
        hours = ~~(minutes / 60);
        minutes = minutes - hours * 60;
    }

    if (hours >= 24) {
        days = ~~(hours / 24);
        hours = hours - days * 24;
    }

    return {DD: days, HH: hours, MM: minutes, SS: seconds};
};

exports.fixXhr = function (options, isBos) {
    return function (httpMethod, resource, args, config) {
        var client = this;
        var endpointHost = urlModule.parse(config.endpoint).host;

        // x-bce-date 和 Date 二选一，是必须的
        // 但是 Flash 无法设置 Date，因此必须设置 x-bce-date
        args.headers['x-bce-date'] = new Date().toISOString().replace(/\.\d+Z$/, 'Z');
        args.headers.host = endpointHost;

        // Flash 的缓存貌似比较厉害，强制请求新的
        args.params['.stamp'] = new Date().getTime();

        // 只有 PUT 才会触发 progress 事件
        var originalHttpMethod = httpMethod;

        if (httpMethod === 'PUT') {
            // PutObject PutParts 都可以用 POST 协议，而且 Flash 也只能用 POST 协议
            httpMethod = 'POST';
        }

        var xhrUri;
        var xhrMethod = httpMethod;
        var xhrBody = args.body;
        if (httpMethod === 'HEAD') {
            // 因为 Flash 的 URLRequest 只能发送 GET 和 POST 请求
            // getObjectMeta需要用HEAD请求，但是 Flash 无法发起这种请求
            // 所需需要用 relay 中转一下
            // XXX 因为 bucket 不可能是 private，否则 crossdomain.xml 是无法读取的
            // 所以这个接口请求的时候，可以不需要 authorization 字段
            var relayServer = exports.normalizeEndpoint(options.bos_relay_server);
            xhrUri = relayServer + '/' + endpointHost + resource;

            args.params.httpMethod = httpMethod;

            xhrMethod = 'POST';
        }
        else if (isBos === true) {
            xhrUri = exports.transformUrl(config.endpoint + resource);
            args.headers.host = urlModule.parse(xhrUri).host;
        }
        else {
            xhrUri = config.endpoint + resource;
        }

        if (xhrMethod === 'POST' && !xhrBody) {
            // 必须要有 BODY 才能是 POST，否则你设置了也没有
            // 而且必须是 POST 才可以设置自定义的header，GET不行
            xhrBody = '{"FORCE_POST": true}';
        }

        var deferred = sdk.Q.defer();

        var xhr = new mOxie.XMLHttpRequest();

        xhr.onload = function () {
            var response = null;
            try {
                response = JSON.parse(xhr.response || '{}');
            }
            catch (ex) {
                response = {};
            }

            if (xhr.status >= 200 && xhr.status < 300) {
                if (httpMethod === 'HEAD') {
                    deferred.resolve(response);
                }
                else {
                    deferred.resolve({
                        http_headers: {},
                        body: response
                    });
                }
            }
            else {
                deferred.reject({
                    status_code: xhr.status,
                    message: response.message || '',
                    code: response.code || '',
                    request_id: response.requestId || ''
                });
            }
        };

        xhr.onerror = function (error) {
            deferred.reject(error);
        };

        if (xhr.upload) {
            // FIXME(分片上传的逻辑和xxx的逻辑不一样)
            xhr.upload.onprogress = function (e) {
                if (originalHttpMethod === 'PUT') {
                    // POST, HEAD, GET 之类的不需要触发 progress 事件
                    // 否则导致页面的逻辑混乱
                    e.lengthComputable = true;

                    var httpContext = {
                        httpMethod: originalHttpMethod,
                        resource: resource,
                        args: args,
                        config: config,
                        xhr: xhr
                    };

                    client.emit('progress', e, httpContext);
                }
            };
        }

        var promise = client.createSignature(client.config.credentials,
            httpMethod, resource, args.params, args.headers);
        promise.then(function (authorization, xbceDate) {
            if (authorization) {
                args.headers.authorization = authorization;
            }

            if (xbceDate) {
                args.headers['x-bce-date'] = xbceDate;
            }

            var qs = qsModule.stringify(args.params);
            if (qs) {
                xhrUri += '?' + qs;
            }

            xhr.open(xhrMethod, xhrUri, true);

            for (var key in args.headers) {
                if (!args.headers.hasOwnProperty(key)
                    || key === 'host') {
                    continue;
                }
                var value = args.headers[key];
                xhr.setRequestHeader(key, value);
            }

            xhr.send(xhrBody, {
                runtime_order: 'flash',
                swf_url: options.flash_swf_url
            });
        })
        .catch(function (error) {
            deferred.reject(error);
        });

        return deferred.promise;
    };
};


exports.eachLimit = function (tasks, taskParallel, executer, done) {
    var runningCount = 0;
    var aborted = false;
    var fin = false;      // done 只能被调用一次.
    var queue = new Queue(tasks);

    function infiniteLoop() {
        var task = queue.dequeue();
        if (!task) {
            return;
        }

        runningCount++;
        executer(task, function (error) {
            runningCount--;

            if (error) {
                // 一旦有报错，终止运行
                aborted = true;
                fin = true;
                done(error);
            }
            else {
                if (!queue.isEmpty() && !aborted) {
                    // 队列还有内容
                    setTimeout(infiniteLoop, 0);
                }
                else if (runningCount <= 0) {
                    // 队列空了，而且没有运行中的任务了
                    if (!fin) {
                        fin = true;
                        done();
                    }
                }
            }
        });
    }

    taskParallel = Math.min(taskParallel, queue.size());
    for (var i = 0; i < taskParallel; i++) {
        infiniteLoop();
    }
};

exports.inherits = function (ChildCtor, ParentCtor) {
    ChildCtor.prototype = Object.create(ParentCtor.prototype);
    ChildCtor.prototype.constructor = ChildCtor;
};

exports.guessContentType = function (file, opt_ignoreCharset) {
    var contentType = file.type;
    if (!contentType) {
        var object = file.name;
        var ext = object.split(/\./g).pop();
        contentType = sdk.MimeType.guess(ext);
    }

    // Firefox在POST的时候，Content-Type 一定会有Charset的，因此
    // 这里不管3721，都加上.
    if (!opt_ignoreCharset && !/charset=/.test(contentType)) {
        contentType += '; charset=UTF-8';
    }

    return contentType;
};
