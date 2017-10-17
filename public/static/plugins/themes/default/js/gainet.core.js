(function(obj) {
	// 这天，屌丝女神奶茶MM从了高富帅
	var _g = window.g = window.gainet = obj;
	// @params index 默认选中第几个下标
	// @params callback 默认回调函数
	_g.defaultopts = {
		"index" : 0 ,
		"callback" : _g.noop
	};
	_g.callFunc = function(obj, callback, options) {
		if (callback && _g.isFunction(callback)) {
			callback.call(obj, options);
		}
	};
	_g.applyFunc = function(obj, callback, options) {
		if (callback && _g.isFunction(callback)) {
			callback.apply(obj, options);
		}
	};
	_g.js = {
		// par1 url
		// par2 post params
		post : function(url, par) {
			var postDom = document.createElement("form");
			postDom.method = "post";
			postDom.action = url;
			postDom.style.display = "none";
			_g.each(par,function(i, n) {
				if(_g.isArray(n)){
					_g.each(n,function(j, k) {
						var pageInput = document.createElement("input"); // page
						pageInput.setAttribute("name", k.name);
						pageInput.setAttribute("value", k.value);
						postDom.appendChild(pageInput);
					});
				}
				else{
					var pageInput = document.createElement("input"); // page
					pageInput.setAttribute("name", i);
					pageInput.setAttribute("value", n);
					postDom.appendChild(pageInput);
				}
			});
			document.body.appendChild(postDom);
			postDom.submit();
		} ,
		postArr : function(url, par) {
			var postDom = document.createElement("form");
			postDom.method = "post";
			postDom.action = url;
			postDom.style.display = "none";
			_g.each(par,function(i, n) {
				var pageInput = document.createElement("input"); // page
				pageInput.setAttribute("name", n.name);
				pageInput.setAttribute("value", n.value);
				postDom.appendChild(pageInput);
			});
			document.body.appendChild(postDom);
			postDom.submit();
		} ,
		isEmpty : function(obj){
			return obj == null || obj == undefined || obj == '';
		} ,
		isDeclare : function(obj){
			return "undefined" == typeof obj;
		} ,
		stopDefault : function(e) { 
		     if (e && e.preventDefault) {//如果是FF下执行这个
		        e.preventDefault();
		    }else{ 
		        window.event.returnValue = false;//如果是IE下执行这个
		    }
		    return false;
		}
	};
	_g.date = {
		// 日期格式化
		// 格式 YYYY/yyyy/YY/yy 表示年份
		// MM/M 月份
		// W/w 星期
		// dd/DD/d/D 日期
		// hh/HH/h/H 时间
		// mm/m 分钟
		// ss/SS/s/S 秒
		formatDate : function(date, str) {
		    var Week = ['日', '一', '二', '三', '四', '五', '六'];

		    str = str.replace(/yyyy|YYYY/, date.getFullYear());
		    str = str.replace(/yy|YY/, (date.getYear() % 100) > 9 ? (date.getYear() % 100).toString() : '0' + (date.getYear() % 100));
		    var month = date.getMonth() + 1;
		    str = str.replace(/MM/, month > 9 ? month.toString() : '0' + month);
		    str = str.replace(/M/g, month);

		    str = str.replace(/w|W/g, Week[date.getDay()]);

		    str = str.replace(/dd|DD/, date.getDate() > 9 ? date.getDate().toString() : '0' + date.getDate());
		    str = str.replace(/d|D/g, date.getDate());

		    str = str.replace(/hh|HH/, date.getHours() > 9 ? date.getHours().toString() : '0' + date.getHours());
		    str = str.replace(/h|H/g, date.getHours());
		    str = str.replace(/mm/, date.getMinutes() > 9 ? date.getMinutes().toString() : '0' + date.getMinutes());
		    str = str.replace(/m/g, date.getMinutes());

		    str = str.replace(/ss|SS/, date.getSeconds() > 9 ? date.getSeconds().toString() : '0' + date.getSeconds());
		    str = str.replace(/s|S/g, date.getSeconds());
		    return str;

		},

		toDate : function(strTime) {
			return new Date(Date.parse(strTime.replace(/-/g, "/")));
		},

		addMonths : function(date, value) {
			date.setMonth(date.getMonth() + value);
			return date;
		}
	};

})(window.jQuery);