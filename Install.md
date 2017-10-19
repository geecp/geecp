一、	配置GeeCP运行环境

建议使用LNMP环境部署。

Ssh连接服务器

yum install screen

完成后输入

screen -R lnmp

安装lnmp


wget -c http://soft.vpser.net/lnmp/lnmp1.4.tar.gz && tar zxf lnmp1.4.tar.gz && cd lnmp1.4 && ./install.sh lnmp

新增站点

lnmp vhost add

二、	上传文件至服务器

把Geecp代码上传至home区wwwroot目录 yourdomain 目录 

三、	配置环境细节

修改/usr/local/nginx/conf/vhost/yourdomain.conf文件内的yourdomain.com为yourdomain.com/public

修改/usr/local/php/etc/php.ini 引入Geecp的扩展文件

删除/usr/local/nginx/conf/fastcgi.conf中fastcgi_param  REDIRECT_STATUS    200; 下面内容

重启环境 lnmp restart 

四、	赋予执行权限

打开浏览器输入yourdomain.com 按照要求赋予执行权限

五、	注册开发者

打开浏览器，输入https://wwww.geecp.com 注册开发者帐号，并且完成实名认证。

新建站点，输入站点名称和站点地址。新增完成后会显示一条数据，其中包含了需要再安装步骤中输入的站点ID。 


六、	开始安装

打开浏览器输入https://www.yourdomain.com进入安装流程

检测环境
赋予权限
输入站点ID
输入数据库信息
完成安装

七、	完成体验

管理员地址为yourdomain.com/admin


