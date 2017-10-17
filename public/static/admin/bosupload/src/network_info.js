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
 * @file src/network_info.js
 * @author leeight
 */

var utils = require('./utils');

function NetworkInfo() {
    /**
     * 记录从 start 开始已经上传的字节数.
     * @type {number}
     */
    this.loadedBytes = 0;

    /**
     * 记录队列中总文件的大小, UploadComplete 之后会被清零
     * @type {number}
     */
    this.totalBytes = 0;

    /**
     * 记录开始上传的时间.
     * @type {number}
     */
    this._startTime = utils.now();

    this.reset();
}

NetworkInfo.prototype.dump = function () {
    return [
        this.loadedBytes,                     // 已经上传的
        utils.now() - this._startTime,        // 花费的时间
        this.totalBytes - this.loadedBytes    // 剩余未上传的
    ];
};

NetworkInfo.prototype.reset = function () {
    this.loadedBytes = 0;
    this._startTime = utils.now();
};

module.exports = NetworkInfo;
