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

function toUrl(object) {
  var url = 'http://bj.bcebos.com/vod-gauddsywyhn713kc/' + encodeURIComponent(object).replace(/%2F/gi, '/');
  return url;
}

// var AK = getQuery('ak', 'f1a2705d3cf8448cb917684c4f40ac1f');
// var SK = getQuery('sk', '5fd876eb57834c2f8156d8e65890d0fd');
// var DOC_ENDPOINT = getQuery('doc.endpoint', 'http://doc.bce-testinternal.baidu.com');
// var BOS_ENDPOINT = getQuery('bos.endpoint', 'http://bos.qasandbox.bcetest.baidu.com');
// var BOS_BUCKET = getQuery('bos.bucket', 'bkt-gavfz4jzhjtq0y4c');

var AK = getQuery('ak', 'afe4759592064eee930682e399249aba');
var SK = getQuery('sk', '7785ea912b06449f8cbd084998a1e400');
var DOC_ENDPOINT = getQuery('doc.endpoint', 'http://doc.baidubce.com');
var BOS_ENDPOINT = getQuery('bos.endpoint', 'http://bos.bj.baidubce.com');
var BOS_BUCKET = getQuery('bos.bucket', 'bkt-gawizxekph7vrnmb');
var CHUNK_SIZE = '1m';

var doc = new baidubce.sdk.DocClient.Document({
  endpoint: DOC_ENDPOINT,
  credentials: {ak: AK, sk: SK}
});

function getDocKey(file) {
  // source/doc-gdxink1qakahwu6k.txt
  var format = file.name.split('.').pop();
  var name = (Math.random() * Math.pow(2, 32)).toString(36);
  var object = 'source/doc-' + name + '.' + format;

  file.__object = object;

  return object;
}

var uploader = new baidubce.bos.Uploader({
  browse_button: '#file',
  multi_selection: true,
  bos_bucket: BOS_BUCKET,
  bos_endpoint: BOS_ENDPOINT,
  bos_ak: AK,
  bos_sk: SK,
  auto_start: false,
  max_retries: 2,
  max_file_size: '100m',
  bos_multipart_min_size: '10m',
  bos_multipart_parallel: 1,
  chunk_size: CHUNK_SIZE,
  dir_selection: false,
  accept: 'txt,pdf,doc,docx,ppt,pptx,xls,xlsx',
  flash_swf_url: '../bower_components/moxie/bin/flash/Moxie.swf',
  init: {
    FilesFilter: function (_, files) {
      // 添加更多的过滤规则，比如文件大小之类的
    },
    FilesAdded: function (_, files) {
      FilesAdded(_, files);
    },
    BeforeUpload: function (_, file) {
      file.__startTime = new Date().getTime();
      var row = getRowById(file.__id);
      row.setStatus('circle-arrow-up');
    },
    UploadProgress: function (_, file, progress, event) {
      var row = getRowById(file.__id);
      row.setProgress(progress);
    },
    Key: function (_, file) {
      return getDocKey(file);
    },
    FileUploaded: function (_, file, info) {
      var time = ((new Date().getTime() - file.__startTime) / 1000).toFixed(2);
      var row = getRowById(file.__id);
      var url = toUrl(info.body.object);
      row.setStatus('ok-circle', true);
      row.setUrl(url);
      row.setTime(time);

      doc.createFromBos(BOS_BUCKET, file.__object, file.name)
        .then(function (response) {
          row.setMediaId(response.body.documentId);
        })['catch'](function (error) {
          row.setMediaId(String(error));
        });
    },
    UploadComplete: function () {
      // TODO
    },
    ListParts: function (_, file, uploadId) {
      // 恢复断点续传的时候，从本地获取 parts 的信息，避免从服务读取
      // 有时候服务器没有开放读取的权限
      try {
        var parts = localStorage.getItem(uploadId);
        return JSON.parse(parts);
      }
      catch (ex) {
      }
    },
    ChunkUploaded: function (_, file, result) {
      console.log(JSON.stringify(result));
    },
    Error: function (_, error, file) {
      var row = getRowById(file.__id);
      if (error.status_code === 0) {
        row.setStatus('pause', true);
      }
      else {
        row.setStatus('remove-circle', false);
        var errorMessage = $.isPlainObject(error) ? JSON.stringify(error) : String(error);
        row.setErrorMessage(errorMessage);
      }
    }
  }
});

$('button[type=submit]').click(function () {
  uploader.start();
  return false;
});





/* vim: set ts=4 sw=4 sts=4 tw=120: */
