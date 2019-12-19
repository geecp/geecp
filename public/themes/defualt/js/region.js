const $E = function (elem) {
  if (document.querySelectorAll(elem).length == 1) {
    return document.querySelectorAll(elem)[0];
  } else {
    var elems = document.querySelectorAll(elem);
    return elems;
  }
}

function hasClass(obj, cls) {
  var cls = cls || '';
  if (cls.replace(/\s/g, '').length == 0) {
    return false; //当cls没有参数时,返回false;
  } else {
    return new RegExp(' ' + cls + '').test(' ' + obj.className);
  }
}

function isFocus(elemid) {
  if (document.activeElement.id == elemid) {
    return true
  } else {
    return false
  }
}



//表格
const fortbarr = function (arr, id) {
  let html = "";
  for (let index in arr) {
    html += `<th>` + arr[index] + `</th>`
  }
  $E(id).innerHTML = html;
}

//下拉菜单
const dpinner = {
  dp1: function (id, index) {
    let elem = document.querySelectorAll(id + " .btn-dropdown")[index];
    let elem1 = document.querySelectorAll(id + " .dropdown-menu")[index];
    if (elem && elem1) {
      for (let index in elem1.children) {
        elem1.children[index].onclick = function () {
          elem.innerHTML = this.innerHTML;
          this.parentNode.classList.remove('show');
          let sblin = this.parentNode.children;
          for (let index in sblin) {
            if (hasClass(sblin[index], "show", "active")) {
              sblin[index].classList.remove("show", "active");
            }
          }
          this.classList.add("show", "active");
          let str = this.href;
          str = str.split("#");
          let f = str[str.length - 1];
          if ($(".tab_active")) {
            console.log($(".tab_active #" + f).siblings());
            $(".tab_active #" + f).addClass("tab_active_block");
            $(".tab_active #" + f).removeClass("tab_active_none")
            $(".tab_active #" + f).siblings().addClass("tab_active_none")
            $(".tab_active #" + f).siblings().removeClass("tab_active_block")
          }
        }
      }
    }

  },
  dpfor: function (el) {
    if ($E(el).length > 1) {
      for (let index in $E(el)) {
        this.dp1(el, index);
      }
    } else {
      this.dp1(el, 0);
    }
  }
}
dpinner.dpfor(".dropdown");

//帮助hover
let t = null;
$(".hoverhelpshow").hover(function () {
  $E("#hoverhelp").style.display = "block";
  $E("#hoverhelp").classList.add("show");
  clearInterval(t)
}, function () {
  $E("#hoverhelp").classList.remove("show");
  t = setTimeout(function () {
    $E("#hoverhelp").style.display = "none";
  }, 300)

})

//加载城市
const MapCity = {
  str: () => {
    return RULE.CITY()
  },
  add: (e) => {
    $(e).find("#provinces").click(function () {
      $(e).find("#cityes").html(MapCity.cityes($(this).find("option:selected").val()));
    })
  },
  provinces: () => {
    let arr = []
    for (let provin in MapCity.str()) {
      arr.push(provin);
    }
    return arr;
  },
  cityes: (e) => {
    let html = "";
    MapCity.str()[e].map((e) => {
      html += '<option value="' + e + '">' + e + '</option>';
      console.log()
    })
    return html;
  },
  click: (e) => {
    $(".addcity").map((key, e) => {
      let i = 0;
      let html = "";
      MapCity.provinces().map((e) => {
        html += '<option value="' + e + '">' + e + '</option>';
      })
      $(e).find("#provinces").append(html);
      $(e).find("#provinces").click(function () {
        i++;
        if (i == 2) {
          $(e).find("#provinces").children()[0].remove();
        }
        MapCity.add(e);
      })
    })
  }
}
MapCity.click();

//搜索框切换
$(document).on("change", ".search-switch select", function () {
  $(".search-switch input[type=text]")[0].placeholder = "请输入" + $(this).children(":checked").val() + "进行搜索";
})