/**
 * Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
 *
 * Licensed under the Apache License, Version 2.0 (the "License"), you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 *
 * @file events.js
 * @author leeight
 */

module.exports = {
    kPostInit: 'PostInit',
    kKey: 'Key',
    kListParts: 'ListParts',
    kObjectMetas: 'ObjectMetas',
    // kFilesRemoved  : 'FilesRemoved',
    kFileFiltered: 'FileFiltered',
    kFilesAdded: 'FilesAdded',
    kFilesFilter: 'FilesFilter',
    kNetworkSpeed: 'NetworkSpeed',
    kBeforeUpload: 'BeforeUpload',
    // kUploadFile    : 'UploadFile',       // ??
    kUploadProgress: 'UploadProgress',
    kFileUploaded: 'FileUploaded',
    kUploadPartProgress: 'UploadPartProgress',
    kChunkUploaded: 'ChunkUploaded',
    kUploadResume: 'UploadResume', // 断点续传
    // kUploadPause: 'UploadPause',   // 暂停
    kUploadResumeError: 'UploadResumeError', // 尝试断点续传失败
    kUploadComplete: 'UploadComplete',
    kError: 'Error',
    kAborted: 'Aborted'
};
