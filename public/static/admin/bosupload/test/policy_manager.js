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
 * @file test/policy_manager.js
 * @author leeight
 */

var expect = require('expect.js');

var PolicyManager = require('../src/policy_manager');

describe('PolicyManager', function () {
    it('getFromLocal', function () {
        var pm = new PolicyManager({
            bos_credentials: {
                ak: 'ak',
                sk: 'sk'
            }
        });

        return pm.get('bucket').then(function (payload) {
            expect(payload.accessKey).to.eql('ak');
        });
    });
});









