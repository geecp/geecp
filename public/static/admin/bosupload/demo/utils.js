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


var __queryMap = null;
function getQuery(id, opt_defaultValue) {
  if (!__queryMap) {
    __queryMap = {};
    var string = location.search.substr(1);
    if (string) {
      var chunks = string.split('&');
      for (var i = 0; i < chunks.length; i++) {
        var item = chunks[i].split('=');
        var key = item[0];
        var value = item[1];
        if (value) {
          value = decodeURIComponent(value);
        }
        __queryMap[key] = value;
      }
    }
  }
  return __queryMap[id] || opt_defaultValue;
}

var __uuid = 0;
var __isEmpty = true;

function getIdByName(name) {
  return 'f' + (__uuid ++);
}

function getRowById(rowId) {
  return {
    setIgnore: function (ignored) {
      if (ignored) {
        $('#' + rowId).addClass('ignored');
      }
      else {
        $('#' + rowId).removeClass('ignored');
      }
    },
    setProgress: function (progress) {
      $('#' + rowId + ' .f-progress').html((progress * 100).toFixed(2) + '%');
    },
    setStatus: function (type, ok) {
      var container = $('#' + rowId + ' .f-status');
      container.html('<span class="glyphicon glyphicon-' + type + '"></span>');
      if (ok === true) {
        container.css('color', 'green');
      }
      else if (ok === false) {
        container.css('color', 'red');
      }
    },
    setTime: function (time) {
      var container = $('#' + rowId + ' .f-time');
      container.html(time);
    },
    setMediaId: function (mediaId) {
      var container = $('#' + rowId + ' .f-media');
      container.html(mediaId);
    },
    setUrl: function (url) {
      var container = $('#' + rowId + ' .f-name');
      var name = container.html();
      container.html('<a href="' + url + '" target="_blank">' + name + '</a>');
    },
    setErrorMessage: function (errorMessage) {
      var errorHtml = '<div class="alert alert-danger" role="alert">' + errorMessage + '</div>';
      var container = $('#' + rowId + ' .f-name');
      container.append(errorHtml);
    }
  };
}

function FilesAdded(_, files) {
  for (var i = 0; i < files.length; i++) {
    var file = files[i];
    var id = getIdByName(file.name);
    file.__id = id;
    var html = '<tr id="' + id + '">'
               + '<td class="f-id">' + (__uuid) + '</td>'
               + '<td class="f-status"><span class="glyphicon glyphicon-record"></span></td>'
               + '<td class="f-progress">0.00%</td>'
               + '<td class="f-size">' + (humanize.filesize(file.size)) + '</td>'
               + '<td class="f-time">-</td>'
               + '<td class="f-media">-</td>'
               + '<td class="f-name">' + (file.name) + '</td>'
               + '</tr>';
    if (__isEmpty) {
      __isEmpty = false;
      $('table tbody').html(html);
      $('button[type=submit]').attr('disabled', false);
    }
    else {
      $('table tbody').append(html);
    }
  }
}

function getDefaultKey(file) {
  var date = new Date();
  var year = date.getFullYear();

  var month = date.getMonth() + 1;
  if (month < 10) {
    month = '0' + month;
  }
  var day = date.getDate();
  if (day < 10) {
    day = '0' + day;
  }

  var deferred = baidubce.sdk.Q.defer();
  var delay = ~~(2 + Math.random() * 5);    // (2, 7);
  setTimeout(function () {
    var key = year + '/' + month + '/' + day + '/' + file.name;
    deferred.resolve(key);
  }, delay * 100);
  return deferred.promise;
}








/* vim: set ts=4 sw=4 sts=4 tw=120: */
