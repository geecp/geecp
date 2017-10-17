/*
* @Author: pj
* @Date:   2016-06-03 17:15:26
* @Last Modified by:   pj
* @Last Modified time: 2016-09-19 16:19:38
*/

/**
 * [onerror 系统错误]
 * @param  {[String]} errorMessage [错误信息]
 * @param  {[String]} scriptURI    [出错的文件]
 * @param  {[Long]} lineNumber   [出错代码的行号]
 * @param  {[Long]} columnNumber [出错代码的列号]
 * @param  {[Object]} errorObj     [错误的详细信息，Anything]
 * @return {[void]}
 */
window.onerror = function (errorMessage, scriptURI, lineNumber, columnNumber, errorObj) {
  var Sys = {}
  var ua = window.navigator.userAgent.toLowerCase()
  var s
  var catchError

  var res = (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
    (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
    (s = ua.match(/qqbrowser\/([\d.]+)/)) ? Sys.qq = s[1] :
    (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
    (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
    (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0

  try {
    if (!res) {
      return
    }

    if (Sys.ie && parseFloat(Sys.ie) < 8) {
      /* 不支持ie8以下 */
      return
    } else if (Sys.qq && parseFloat(Sys.qq) < 9.2) {
      /* 9.1的版本会报错 */
      return
    } else if (Sys.chrome && parseFloat(Sys.chrome) < 38) {
      /* 低版本的chrome也是要干掉的 */
      return
    } else if (Sys.firefox && parseFloat(Sys.firefox) < 45) {
      /* 低版本的firefox也是要干掉的 */
      return
    }

    if (ua.match(/googlebot/)) {
      /* 过滤爬虫 */
      return
    }

    /* 暂时过滤无意义的信息 */
    if (errorMessage === '对象不支持此属性或方法') {
      return
    }

  }
  catch (e) {
    catchError = e
  }

  var obj = {
    location: location.href,
    errorMessage: errorMessage,
    scriptURI: scriptURI,
    lineNumber: lineNumber,
    columnNumber: columnNumber,
    errorObj: errorObj,
    userAgent: ua,
    catchError: catchError
  }

  if(console){
    console.log(obj)
  }

  if(scriptURI){
    var xmlHttp = new XMLHttpRequest()
    xmlHttp.open("POST", "/report", true)
    xmlHttp.setRequestHeader("Content-type","application/json")
    xmlHttp.send(JSON.stringify(obj))
  }

  return true
}
