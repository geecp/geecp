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
 * @file test/sts_token_manager.js
 * @author leeight
 */

var sdk = require('bce-sdk-js');
var expect = require('expect.js');

var StsTokenManager = require('../src/sts_token_manager');

describe('StsTokenManager', function () {
    this.timeout(60 * 1000);

    it('get', function () {
        var stm = new StsTokenManager({
            uptoken_url: 'xx',
            uptoken_jsonp_timeout: 1000
        });
        stm._getImpl = function (bucket) {
            var options = this.options;
            var deferred = sdk.Q.defer();
            setTimeout(function () {
                deferred.resolve({
                    AccessKeyId: 'ak',
                    SecretAccessKey: 'sak',
                    SessionToken: 'st',
                    Expiration: 'ex',
                    uptoken_url: options.uptoken_url
                });
            }, this.options.uptoken_jsonp_timeout);
            return deferred.promise;
        };

        return stm.get('bucket').then(function (payload) {
            expect(payload.AccessKeyId).to.eql('ak');
            expect(payload.SecretAccessKey).to.eql('sak');
        });
    });
});









