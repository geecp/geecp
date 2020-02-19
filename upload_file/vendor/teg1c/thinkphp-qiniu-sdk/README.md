thinkphp-qiniu-sdk

基于tp5.1框架的七牛云存储实现，实现文件上传，文件管理功能

composer 安装

```composer require teg1c/thinkphp-qiniu-sdk```


如果该方法安装不成功，请在项目根目录下的composer.json的require中添加

```"teg1c/thinkphp-qiniu-sdk": "dev-master"```

然后使用cmd进入项目根目录下运行composer update



配置使用
===============


## 配置：


在tp5.1的配置文件app.php中配置七牛云的配置参数
```
'qiniu' => [

        'accesskey' => '你自己的七牛云accesskey',
        'secretkey' => '你自己的七牛云secretkey',
        'bucket' => 'bucket',
 ]
```
## 使用

```
use tegic\qiniu\Qiniu;
try{
      
      $qiniu = new Qiniu();
      $result = $qiniu->upload();
      dump($result);
    }catch (Exception $e){
      
      dump($e->getMessage());
    }
```
 
上传成功则返回的是key值为文件名


## 直接使用

```
  try{
  
      $qiniu = new Qiniu('你自己的七牛云accesskey','你自己的七牛云secretkey','你自己创建的bucket');
      $result = $qiniu->upload();
      
 }catch (Exception $e){
 
      dump($e->getMessage());
 }
```
---
说明：
- 修改了七牛参数配置请清除一下缓存
- upload()方法支持参数传入。可传入第一个参数为要上传文件保存的名称，第二个参数为bucket名称。
 
 第一个参数默认取文件的hash串拼接时间戳time()
 
 第二个参数默认为配置里的bucket


如果使用中有任何错误或者疑问可以给我发邮件：i@izww.cn

