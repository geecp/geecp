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
 * @file policy_manager.js
 * @author leeight
 */

var sdk = require('bce-sdk-js');

var utils = require('./utils');

function PolicyManager(options) {
    this.options = options;
    this._cache = {};
}

PolicyManager.prototype.get = function (bucket) {
    var self = this;

    if (self._cache[bucket] != null) {
        return sdk.Q.resolve(self._cache[bucket]);
    }

    return sdk.Q.resolve(this._getImpl(bucket)).then(function (payload) {
        self._cache[bucket] = payload;
        return payload;
    });
};

PolicyManager.prototype._getImpl = function (bucket) {
    var bucketPolicy = utils.getDefaultPolicy(bucket);

    var options = this.options;
    if (options.bos_credentials && !options.uptoken) {
        // 如果没有 options.uptoken，说明不是 临时 ak 和 sk
        var credentials = options.bos_credentials;
        return this._getFromLocal(bucketPolicy, credentials);
    }

    return this._getFromRemote(bucketPolicy);
};

PolicyManager.prototype._getFromLocal = function (bucketPolicy, credentials) {
    var auth = new sdk.Auth(credentials.ak, credentials.sk);
    var policyBase64 = new Buffer(JSON.stringify(bucketPolicy)).toString('base64');
    var policySignature = auth.hash(policyBase64, credentials.sk);
    var policyAccessKey = credentials.ak;

    return {
        policy: policyBase64,
        signature: policySignature,
        accessKey: policyAccessKey
    };
};

PolicyManager.prototype._getFromRemote = function (bucketPolicy) {
    var options = this.options;
    var uptoken_url = options.uptoken_url;
    var timeout = options.uptoken_timeout || options.uptoken_jsonp_timeout;
    var viaJsonp = options.uptoken_via_jsonp;

    var deferred = sdk.Q.defer();
    $.ajax({
        url: uptoken_url,
        jsonp: viaJsonp ? 'callback' : false,
        dataType: viaJsonp ? 'jsonp' : 'json',
        timeout: timeout,
        data: {
            policy: JSON.stringify(bucketPolicy)
        },
        success: function (payload) {
            // payload.policy (base64)
            // payload.signature
            // payload.accessKey
            deferred.resolve(payload);
        },
        error: function () {
            deferred.reject(new Error('Get policy signature timeout (' + timeout + 'ms).'));
        }
    });

    return deferred.promise;
};

module.exports = PolicyManager;
