function addEvent(_object ,_type, _fn, _fn2){
  if (window.addEventListener) {
    _object.addEventListener(_type, _fn, false)
  }else {
    _object.attachEvent('on' + _type, _fn)
  }
}

function getTop (node) {
  var top = 0
  top = node.offsetTop
  while (node.tagName !== 'BODY') {
    node = node.parentNode
    top = top + node.offsetTop
  }
  return top
}

var addClass = function (node, str) {
  var className = node.className
  if (str.indexOf(className) === -1) {
    node.className = node.className  + ' '+ str
  }
}

var removeClass = function (node, str) {
  var className = node.className
  if (str.indexOf(className) === -1) {
    node.className = node.className.replace(str,'')
  }
}