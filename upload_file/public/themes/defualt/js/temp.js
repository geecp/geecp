const TEMPS = {
    /** 
     * @name 工单附件列表
     * @param   {Object}    e    列表数据
     * @param   {Number}   type 返回格式类型 0 为图片类型 1为文件类型
     */
    ticketAttachs: (e, type = 0) => {
        let items = '';
        if (typeof e === 'string') {
            if (type === 1) {
                items = `<li class="list-inline-item">
                          <a class="iconfont icon-TXTtubiao" href="${window.FILTERBOSURL+e}"></a>
                      </li>`;
            } else if (type === 2) {

            } else if (type === 0) {
                items = `<li class="list-inline-item" data-files="${e}">
                          <a href="${window.FILTERBOSURL+e}" target="_blank" style="background: url(${window.FILTERBOSURL+e});background-size: cover;background-position: center center;"></a>
                      </li>`;
            }
            return items;
        }
        if (type === 1) {
            items = `<li class="list-inline-item">
                      <span class="iconfont icon-icon_yichu closes" onclick="delFiles($(this))"></span>
                      <a class="iconfont icon-TXTtubiao" href="${e.data}"></a>
                  </li>`;
        } else if (type === 2) {

        } else if (type === 0) {
            items = `<li class="list-inline-item" data-files="${e.data.split(window.FILTERBOSURL).join("")}">
                      <span class="iconfont icon-icon_yichu closes" onclick="delFiles($(this))"></span>
                      <a href="${e.data}" target="_blank" style="background: url(${e.data});background-size: cover;background-position: center center;"></a>
                  </li>`;
        }
        return items;
    },
    sidebarchild: () => {
        return ``;
    },
    nav_bar: () => {
        return ``
    },
    safety_certificate: () => {
        return `<ul class="sidebar-ul sidebar-ul-li ul-liside ">
        <li >
            <div class="sidebar-item fs-16 border-t">
                安全认证
            </div>
        </li>
        <li>
            <a href="accesslist.html" class="sidebar-item text-dark">
                <span>Access Key</span>
            </a>
        </li>
        
    </ul>`
    },
    server_bcd: () => {
        return `<ul class="sidebar-ul sidebar-ul-li ul-liside ">
        <li >
            <div class="sidebar-item fs-16 border-t">
            域名概览
            </div>
        </li>
        <li>
            <a href="overview.html" class="sidebar-item text-dark">
                <span>域名概览</span>
            </a>
        </li>
        <li>
            <a href="manage_list.html" class="sidebar-item text-dark">
                <span>域名管理</span>
            </a>
        </li>
        <li>
            <a href="domain-rollin-list.html" class="sidebar-item text-dark">
                <span>域名转入</span>
            </a>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>域名交易</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="fixedprice-domain.html"class="sidebar-item text-dark" >
                        <span>一口价域名</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="domain-price.html" class="sidebar-item text-dark">
                <span>域名价格</span>
            </a>
        </li>
        <li>
            <a href="message-demo.html" class="sidebar-item text-dark">
                <span>信息模板</span>
            </a>
        </li>
        <li>
            <a href="discounts-pack-list.html" class="sidebar-item text-dark">
                <span>优惠资源包</span>
            </a>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>订单管理</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="../billing/order_list.html"class="sidebar-item text-dark" >
                        <span>待付款订单</span>
                    </a>
                </li>
                <li>
                    <a href="../billing/order_list.html"class="sidebar-item text-dark" >
                        <span>已付款订单</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>工具与服务</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="#"class="sidebar-item text-dark" >
                        <span> ICP备案</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>`
    },
    server_dns: () => {
        return `<ul class="sidebar-ul sidebar-ul-li ul-liside ">
        <li >
            <div class="sidebar-item fs-14 border-t  pl-4 pr-0">
            DNS智能云解析
            </div>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>公网DNS服务</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="dnsanalysis-server.html"class="sidebar-item text-dark" >
                        <span>解析服务</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>订单管理</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="../billing/order_list.html"class="sidebar-item text-dark" >
                        <span>待付款订单</span>
                    </a>
                </li>
                <li>
                    <a href="../billing/order_list.html"class="sidebar-item text-dark" >
                        <span>已付款订单</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>工具与服务</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="#"class="sidebar-item text-dark" >
                        <span> ICP备案</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>`
    },
    server_ssl: () => {
        return `<ul class="sidebar-ul sidebar-ul-li ul-liside ">
        <li >
            <div class="sidebar-item border-t fs-16 pl-4">
            证书申请服务
            </div>
        </li>
        <li>
            <a href="purchased_list.html" class="sidebar-item text-dark">
                <span>已购证书列表</span>
            </a>
        </li>
        <li>
            <a href="../iam/accesslist.html" class="sidebar-item text-dark">
                <span>证书信息管理</span>
            </a>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>订单管理</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="../billing/order_list.html"class="sidebar-item text-dark" >
                        <span>待付款订单</span>
                    </a>
                </li>
                <li>
                    <a href="../billing/order_list.html"class="sidebar-item text-dark" >
                        <span>已付款订单</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>`
    },
    server_vps: () => {
        return `<ul class="sidebar-ul sidebar-ul-li ul-liside ">
        <li >
            <div class="sidebar-item border-t fs-16 pl-4">
                云主机VPS
            </div>
        </li>
        <li>
            <a href="#" class="sidebar-item text-dark">
                <span>专属服务器</span>
            </a>
        </li>
        <li>
            <a href="../vps/cloudhost-vps.html" class="sidebar-item text-dark">
                <span>实例</span>
            </a>
        </li>
        <li>
            <a href="#" class="sidebar-item text-dark">
                <span>磁盘</span>
            </a>
        </li>
        <li>
            <a href="#" class="sidebar-item text-dark">
                <span>镜像</span>
            </a>
        </li>
        <li>
            <div class="sidebar-item active">
                <span>快照</span>
                <i class="iconfont iconicon_jiantou-you float-right"></i>
            </div>
            <ul class="sidebar-item-child">
                <li>
                    <a href="#"class="sidebar-item text-dark" >
                        <span>快照列表</span>
                    </a>
                </li>
                <li>
                    <a href="#"class="sidebar-item text-dark" >
                        <span>自动快照策略</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="#" class="sidebar-item text-dark">
                <span>安全组</span>
            </a>
        </li>
        <li>
            <a href="#" class="sidebar-item text-dark">
                <span>操作日志</span>
            </a>
        </li>
        <li>
            <a href="#" class="sidebar-item text-dark">
                <span>秘钥对</span>
            </a>
        </li>
    </ul>`
    },
    server_sms: () => {
        return `<ul class="sidebar-ul sidebar-ul-li ul-liside ">
        <li >
            <div class="sidebar-item border-t fs-14 pl-4 pr-0">
            消息服务SMS
            </div>
        </li>
        <li>
            <a href="sms_overview.html" class="sidebar-item text-dark">
                <span>SMS概览</span>
            </a>
        </li>
        <li>
            <a href="signature_list.html" class="sidebar-item text-dark">
                <span>短信签名管理</span>
            </a>
        </li>
        <li>
            <a href="template_list.html" class="sidebar-item text-dark">
                <span>短信模板</span>
            </a>
        </li>
        <li>
            <a href="sendlog_list.html" class="sidebar-item text-dark">
                <span>已发送消息</span>
            </a>
        </li>
    </ul>`
    },
}