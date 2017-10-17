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
 * @file append_object_task.js
 * @author leeight
 */

var async = require('async');
var u = require('underscore');
var sdk = require('bce-sdk-js');

var utils = require('./utils');
var events = require('./events');
var Task = require('./task');

function AppendObjectTask() {
    Task.apply(this, arguments);
}
utils.inherits(AppendObjectTask, Task);

AppendObjectTask.prototype.start = function () {
    if (this.aborted) {
        return sdk.Q.resolve();
    }

    var self = this;

    var dispatcher = this.eventDispatcher;

    var file = this.options.file;
    var bucket = this.options.bucket;
    var object = this.options.object;
    var metas = this.options.metas;
    var chunkSize = this.options.chunk_size;

    // 调用 getObjectMeta 接口获取 x-bce-next-append-offset 和 x-bce-object-type
    // 如果 x-bce-object-type 不是 "Appendable"，那么就不支持断点续传了
    var contentType = utils.guessContentType(file);

    return this._getObjectMetadata(bucket, object)
        .then(function (response) {
            var httpHeaders = response.http_headers;
            var appendable = utils.isAppendable(httpHeaders);
            if (!appendable) {
                // Normal Object 不能切换为 Appendable Object
                dispatcher.dispatchEvent(events.kUploadProgress, [file, 1, null]);
                return sdk.Q.resolve();
            }

            var contentLength = +(httpHeaders['content-length']);
            if (contentLength >= file.size) {
                // 服务端的文件不小于本地，就没必要上传了
                dispatcher.dispatchEvent(events.kUploadProgress, [file, 1, null]);
                return sdk.Q.resolve();
            }

            // offset 和 content-length 应该是一样大小的吧？
            var offset = +httpHeaders['x-bce-next-append-offset'];

            // 上传进度
            var progress = file.size <= 0 ? 0 : offset / file.size;
            dispatcher.dispatchEvent(events.kUploadProgress, [file, progress, null]);

            // 排除了 offset 之后，按照 chunk_size 切分文件
            // XXX 一般来说，如果启用了 (bos_appendable)，就可以考虑把 chunk_size 设置为一个比较小的值
            var tasks = utils.getAppendableTasks(file.size, offset, chunkSize);

            var deferred = sdk.Q.defer();
            async.mapLimit(tasks, 1, function (item, callback) {
                var offset = item.start;
                var offsetArgument = offset > 0 ? offset : null;
                var blob = file.slice(offset, item.stop + 1);
                var resolve = function (response) {
                    var progress = (item.stop + 1) / file.size;
                    dispatcher.dispatchEvent(events.kUploadProgress, [file, progress, null]);
                    callback(null, response);
                };
                var reject = function (error) {
                    callback(error);
                };
                var options = u.extend({
                    'Content-Type': contentType,
                    'Content-Length': item.partSize
                }, metas);

                self.xhrRequesting = self.client.appendObjectFromBlob(bucket, object,
                    blob, offsetArgument, options);
                return self.xhrRequesting.then(resolve, reject);
            },
            function (err, results) {
                if (err) {
                    deferred.reject(err);
                }
                else {
                    deferred.resolve(results);
                }
            });
            return deferred.promise;
        })
        .then(function () {
            var response = {
                http_headers: {},
                body: {
                    bucket: bucket,
                    object: object
                }
            };
            dispatcher.dispatchEvent(events.kFileUploaded, [file, response]);
        })
        .catch(function (error) {
            var eventType = self.aborted ? events.kAborted : events.kError;
            dispatcher.dispatchEvent(eventType, [error, file]);
            return sdk.Q.resolve();
        });
};

AppendObjectTask.prototype._getObjectMetadata = function (bucket, object) {
    return this.client.getObjectMetadata(bucket, object)
        .catch(function (error) {
            if (error.status_code === 404) {
                // 文件不存在，可以上传一个新的了
                return {
                    http_headers: {
                        'content-length': 0,
                        'x-bce-next-append-offset': 0,
                        'x-bce-object-type': 'Appendable'
                    },
                    body: {}
                };
            }

            throw error;
        });
};


module.exports = AppendObjectTask;
