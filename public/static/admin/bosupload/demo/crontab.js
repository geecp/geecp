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
 */

var sdk = require('baidubce-sdk');
var async = require('async');

var config = {
    endpoint: process.env.BOS_ENDPOINT || 'http://bos.bj.baidubce.com',
    credentials: {
        ak: process.env.BOS_AK,
        sk: process.env.BOS_SK
    }
};

var bucket = 'baidubce';
var client = new sdk.BosClient(config);

function deleteObject(item, callback) {
    client.deleteObject(bucket, item.key)
        .then(function (response) {
            console.log('DELETE bos://%s/%s', bucket, item.key);
            callback(null, response);
        })
        .catch(function (error) {
            callback(error);
        });
}

client.listObjects(bucket).then(function (response) {
    var contents = response.body.contents || [];
    async.eachLimit(contents, 5, deleteObject, function (error) {
        if (error) {
            throw error;
        }
    });
});









/* vim: set ts=4 sw=4 sts=4 tw=120: */
