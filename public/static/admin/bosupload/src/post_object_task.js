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
 * @file post_object_task.js
 * @author leeight
 */

var u = require('underscore');
var sdk = require('bce-sdk-js');

var utils = require('./utils');
var events = require('./events');
var Task = require('./task');

function PostObjectTask() {
    Task.apply(this, arguments);

    this._policyManager = null;
}
utils.inherits(PostObjectTask, Task);

PostObjectTask.prototype.setPolicyManager = function (policyManager) {
    this._policyManager = policyManager;
};

PostObjectTask.prototype.start = function (opt_maxRetries) {
    if (this.aborted) {
        return sdk.Q.resolve();
    }

    var self = this;

    var dispatcher = this.eventDispatcher;

    var file = this.options.file;
    var bucket = this.options.bucket;
    var object = this.options.object;

    var config = this.client.config;

    return this._policyManager.get(bucket)
        .then(function (payload) {
            // 因为 FLASH 会自动请求 /crossdomain.xml，所以我们需要把
            // http://bj.bcebos.com/<bucket>/ 的上传请求改成
            // http://<bucket>.bj.bcebos.com
            var url = config.endpoint.replace(/^(https?:\/\/)/, '$1' + bucket + '.');

            var fields = {
                'Content-Type': utils.guessContentType(file, true),
                'key': object,
                'policy': payload.policy,
                'signature': payload.signature,
                'accessKey': payload.accessKey,
                'success-action-status': '201'
            };

            return self._sendPostRequest(url, fields, file);
        })
        .then(function (response) {
            response.body.bucket = bucket;
            response.body.object = object;
            dispatcher.dispatchEvent(events.kFileUploaded, [file, response]);
        })
        .catch(function (error) {
            var eventType = self.aborted ? events.kAborted : events.kError;
            dispatcher.dispatchEvent(eventType, [error, file]);
        });
};

PostObjectTask.prototype._sendPostRequest = function (url, fields, file) {
    var self = this;
    var dispatcher = this.eventDispatcher;

    var deferred = sdk.Q.defer();

    if (typeof mOxie === 'undefined'
        || !u.isFunction(mOxie.FormData)
        || !u.isFunction(mOxie.XMLHttpRequest)) {
        return sdk.Q.reject(new Error('mOxie is undefined.'));
    }

    var formData = new mOxie.FormData();
    u.each(fields, function (value, name) {
        if (value == null) {
            return;
        }
        formData.append(name, value);
    });
    formData.append('file', file);

    var xhr = this.xhrRequesting = new mOxie.XMLHttpRequest();
    xhr.onload = function (e) {
        if (xhr.status >= 200 && xhr.status < 300) {
            deferred.resolve({
                http_headers: {},
                body: {}
            });
        }
        else {
            deferred.reject(new Error('Invalid response statusCode ' + xhr.status));
        }
    };
    xhr.onerror = function (error) {
        deferred.reject(error);
    };
    xhr.onabort = function () {
        deferred.reject(new Error('xhr was aborted.'));
    };
    if (xhr.upload) {
        xhr.upload.onprogress = function (e) {
            var progress = e.loaded / e.total;
            self.networkInfo.loadedBytes += (e.loaded - file._previousLoaded);
            file._previousLoaded = e.loaded;
            dispatcher.dispatchEvent(events.kNetworkSpeed, self.networkInfo.dump());
            dispatcher.dispatchEvent(events.kUploadProgress, [file, progress, null]);
        };
    }
    xhr.open('POST', url, true);
    xhr.send(formData, {
        runtime_order: 'flash',
        swf_url: self.options.flash_swf_url
    });

    return deferred.promise;
};


module.exports = PostObjectTask;
