### Baidu Cloud Engine BOS Uploader

bce-bos-uploader 是基于 [bce-sdk-js](https://github.com/baidubce/bce-sdk-js) 开发的一个 ui 组件，易用性更好。

DEMO地址：<http://leeight.github.io/bce-bos-uploader/>

### 支持的浏览器

1. 基于Xhr2和[File API](http://caniuse.com/#feat=fileapi)，可以支持：IE10+, Firefox/Chrome/Opera 最新版
2. 借助[mOxie](https://github.com/moxiecode/moxie)，可以支持IE低版本（6,7,8,9）

### 如何使用

```
bower install bce-bos-uploader
```

写一个最简单的页面：

```html
<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>bce-bos-uploader simple demo</title>
    <!--[if lt IE 8]><script src="./bower_components/json3/lib/json3.min.js"></script><![endif]-->
    <!--[if lt IE 9]><script src="./bower_components/js-polyfills/es5.js"></script><![endif]-->
    <!--[if lt IE 10]><script src="./bower_components/moxie/bin/js/moxie.js"></script><![endif]-->
    <script src="./bower_components/jquery/dist/jquery.min.js"></script>
    <script src="./bower_components/bce-bos-uploader/bce-bos-uploader.bundle.js"></script>
  </head>
  <body>
    <input type="file" id="file"
           data-multi_selection="true"
           data-bos_bucket="baidubce"
           data-uptoken_url="http://127.0.0.1:1337/ack" />
    <script>new baidubce.bos.Uploader('#file');</script>
  </body>
</html>
```

> 关于 uptoken_url 应该如何实现，以及如何设置过 Bucket 的 CORS 属性，请参考 bce-sdk-js 的文档：[在浏览器中直接上传文件到bos](http://baidubce.github.io/bce-sdk-js/docs/advanced-topics-basic-example-in-browser.html#content) 和 [服务端签名](http://baidubce.github.io/bce-sdk-js/docs/advanced-topics-server-signature.html#content)

当然，也可以去掉 html tag 里面的 data 属性，直接用JS的方式来初始化：

```html
<input type="file" id="file" />
<script>
var uploader = new baidubce.bos.Uploader({
  browse_button: '#file',
  bos_bucket: 'baidubce',
  multi_selection: true,
  uptoken_url: 'http://127.0.0.1:1337/ack'
});
</script>
```


### 支持的配置参数

|*名称*|*是否必填*|*默认值*|*说明*|
|-----|---------|-------|-----|
|bos_bucket|Y|无|需要上传到的Bucket|
|uptoken_url|Y|无|用来进行服务端签名的URL，需要支持JSONP|
|browse_button|Y|无|需要初始化的`<input type="file"/>`|
|bos_endpoint|N|http://bos.bj.baidubce.com|BOS服务器的地址|
|bos_ak|N|无|如果没有设置`uptoken_url`的话，必须有`ak`和`sk`这个配置才可以工作|
|bos_sk|N|无|如果没有设置`uptoken_url`的话，必须有`ak`和`sk`这个配置才可以工作|
|bos_appendable|N|false|是否采用Append的方式上传文件**不支持IE低版本**|
|bos_task_parallel|N|3|队列中文件并行上传的个数|
|uptoken|N|无|sts token的内容|
|get_new_uptoken|N|true|如果设置为false，会自动获取到Sts Token，上传的过程中可以减少一些请求|
|auth_stripped_headers|N|['User-Agent', 'Connection']|如果计算签名的时候，需要剔除一些headers，可以配置这个参数|
|multi_selection|N|false|是否可以选择多个文件|
|dir_selection|N|false|是否允许选择目录(有些浏览器开启了这个选型之后，只能选择目录，无法选择文件)|
|max_retries|N|0|如果上传文件失败之后，支持的重试次数。默认不重试|
|auto_start|N|false|选择文件之后，是否自动上传|
|max_file_size|N|100M|可以选择的最大文件，超过这个值之后，会被忽略掉|
|bos_multipart_min_size|N|10M|超过这个值之后，采用分片上传的策略。如果想让所有的文件都采用分片上传，把这个值设置为0即可|
|chunk_size|N|4M|分片上传的时候，每个分片的大小（如果没有切换到分片上传的策略，这个值没意义）|
|bos_multipart_auto_continue|N|true|是否开启断点续传，如果设置成false，则UploadResume和UploadResumeError事件不会生效|
|bos_multipart_local_key_generator|N|defaults|计算localStorage里面key的策略，可选值有`defaults`和`md5`|
|accept|-|-|可以支持选择的文件类型|
|flash_swf_url|-|-|mOxie Flash文件的地址|

#### 关于 bos_policy

BOS为了支持低版本的IE浏览器，开发了 PostObject 接口，简单来说，就是支持通过 Form 表单的形式来直接把文件上传到 BOS。为了保证安全性，一般我们的 bucket 权限都是 `public-read`，因此在上传的表单里面添加必须的字段 `policy` 和 `signature`，对应到我们的配置项里面，就是 `bos_policy` 和 `bos_policy_signature`。

其中 `bos_policy` 的默认值是

```
{
  "expiration": "当前时间 + 24小时",
  "conditions": [
    {"bucket": "配置项里面的 bos_bucket 的名字"}
  ]
}
```

`conditions` 还支持的参数有`key` 和 `content-length-range`，例如：

```
{
  "expiration": "当前时间 + 24小时",
  "conditions": [
    {"bucket": "配置项里面的 bos_bucket 的名字"},
    {"key": "abc*"},
    ["content-length-range", 0, 100]
  ]
}
```

`bos_policy_signature`是通过 `sk` 在后端对 `bos_policy` 进行签名得到的结果，简单来说，算法是这样子的

```js
var crypto = require('crypto');

var sk = 'xxx';
var policyBase64 = new Buffer(JSON.stringify(policy)).toString('base64');
var sha256Hmac = crypto.createHmac('sha256', sk);
sha256Hmac.update(policyBase64);
var signature = sha256Hmac.digest('hex');
```

flash_swf_url 是 [mOxie](https://github.com/moxiecode/moxie) 提供的在低版本IE下面，通过 Flash 来模拟 XMLHttpRequest 和 FormData 接口的文件。

如果把 bucket 的权限设置成了 public-read-write，那么其实任何人都可以往 bucket 里面上传文件了，此时就不需要有 bos_policy 了，需要显示的设置成 `null`.

如果只设置了 bos_policy, 那么在需要 bos_policy_signature 的时候，会通过 uptoken_url 发起 JSONP 请求向后端来获取，需要返回的数据格式是：

```
jsonp({
  policy: 'xx',
  signature: 'yy',
  accessKey: 'zz'
});
```

### 支持的事件

在初始化 uploader 的时候，可以通过设置 init 来传递一些 回掉函数，然后 uploader 在合适的时机，会调用这些回掉函数，然后传递必要的参数。例如：

```js
var uploader = new baidubce.bos.Uploader({
  init: {
    PostInit: function () {
      // uploader 初始化完毕之后，调用这个函数
    },
    Key: function (_, file) {
      // 如果需要重命名 BOS 存储的文件名称，这个函数
      // 返回新的文件名即可
      // 如果这里需要执行异步的操作，可以返回 Promise 对象
      // 如果需要自定义bucket和object，可以返回{bucket: string, key: string}
      // 例如：
      // return new Promise(function (resolve, reject) {
      //   setTimeout(function () {
      //     resolve(file.name);
      //   }, 2000);
      // });
    },
    FilesAdded: function (_, files) {
      // 当文件被加入到队列里面，调用这个函数
    },
    FilesFilter: function (_, files) {
      // 如果需要对加入到队列里面的文件，进行过滤，可以在
      // 这个函数里面实现自己的逻辑
      // 返回值需要是一个数组，里面保留需要添加到队列的文件
    },
    BeforeUpload: function (_, file) {
      // 当某个文件开始上传的时候，调用这个函数
      // 如果想组织这个文件的上传，请返回 false
    },
    UploadProgress: function (_, file, progress, event) {
      // 文件的上传进度
    },
    NetworkSpeed: function (_, bytes, time, pendings) {
      var speed = bytes / time;             // 上传速度
      var leftTime = pendings / (speed);    // 剩余时间
      console.log(speed, leftTime);
    },
    FileUploaded: function (_, file, info) {
      // 文件上传成功之后，调用这个函数
      var url = [bos_endpoint, info.body.bucket, info.body.object].join('/');
      console.log(url);
    },
    UploadPartProgress: function (_, file, progress, event) {
      // 分片上传的时候，单个分片的上传进度
    },
    ChunkUploaded: function (_, file, result) {
      // 分片上传的时候，单个分片上传结束
    },
    Error: function (_, error, file) {
      // 如果上传的过程中出错了，调用这个函数
    },
    UploadComplete: function () {
      // 队列里面的文件上传结束了，调用这个函数
    },
    UploadResume: function (_, file, partList, event) {
      // 断点续传生效时，调用这个函数，partList表示上次中断时，已上传完成的分块列表
    },
    UploadResumeError: function (_, file, error, event) {
      // 尝试进行断点续传失败时，调用这个函数
    }
  }
});
```

> 需要注意的时候，所以回掉函数里面的一个参数，暂时都是 null，因此上面的例子中用 _ 代替，后续可能会升级


### 对外提供的接口


#### start()

当 auto_start 设置为 false 的时候，需要手工调用 `start` 来开启上传的工作。

#### stop()

调用 stop 之后，会终止对文件队列的处理。需要注意的是，不是立即停止上传，而是等到当前的文件处理结束（成功/失败）之后，才会停下来。
