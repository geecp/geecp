/**
 * Created by Administrator on 2019/3/22.
 */
//host页面2的固定右侧购买框
$(function() {
    $(window).scroll(function() {
        if ($(window).scrollTop() >= ($(".fixed-top").height() + $(".breadcrumb").height() + $(".card-header").height() + 15)) {
            $("#hostscroll1").addClass("hostfixed");
            $("#hostscroll2").addClass("d-block");
        } else {
            $("#hostscroll1").removeClass("hostfixed");
            $("#hostscroll2").removeClass("d-block");
        };
        $("#hostscroll1").width($("#hostscroll2").width());
    })
});


//导航菜单里点击消息触发右侧内容滑入,再次点击滑出
$(function() {
    var count = 1;
    $("#message-click").on("click", function() {
        count++ % 2 ?
            (function() {
                $("#message-frame").css("right", "0");
            }()) :
            (function() {
                $("#message-frame").css("right", "-500px");
            }());
        $("#closemessage").on("click", function() {
            $("#message-frame").css("right", "-500px");
            count++;
        });
    });
});
//鼠标移入到滑入的消息框上，“设为已读”按钮显示
$(function() {
    $(".set-readed").on("mouseenter", function() {
        $(this).children("ul").children("li").eq(1).addClass("d-none");
        $(this).children("ul").children("li").eq(2).removeClass("d-none");
    }).on("mouseleave", function() {
        $(this).children("ul").children("li").eq(1).removeClass("d-none")
        $(this).children("ul").children("li").eq(2).addClass("d-none")
    });
});

//工单列表页面搜索选择切换
$(function() {
    $("#order-click").on("change", function() {
        if ($(this).val() == "gjz") {
            $("#change-22").removeClass("d-none").prev().addClass("d-none").next().next().addClass("d-none");
        } else if ($(this).val() == "zt") {
            $("#change-33").removeClass("d-none").prev().addClass("d-none").prev().addClass("d-none");
        } else if ($(this).val() == "gdbh") {
            $("#change-11").removeClass("d-none").next().addClass("d-none").next().addClass("d-none");
        }
    })
});

//主机创建页面主机套餐选择，下方介绍字体切换
$(function() {
        $("#host-model").on("change", function() {
            if ($(this).val() == "BC01") {
                $(".BC01").removeClass("d-none").siblings().addClass("d-none");
            } else if ($(this).val() == "BC02") {
                $(".BC02").removeClass("d-none").siblings().addClass("d-none");
            } else if ($(this).val() == "BC03") {
                $(".BC03").removeClass("d-none").siblings().addClass("d-none");
            } else if ($(this).val() == "BC04") {
                $(".BC04").removeClass("d-none").siblings().addClass("d-none");
            } else if ($(this).val() == "BC05") {
                $(".BC05").removeClass("d-none").siblings().addClass("d-none");
            }
        })
    })
    //域名服务discounts-pack-buy页面点击盒子，阴影效果
$(function() {
        $("#discounts").children("div").on("click", function() {
            $(this).addClass("discounts-shadow").siblings().removeClass("discounts-shadow");
        })
    })
    //信息模板列表添加信息模板点击企业radio效果,及select选框操作
$(function() {
        $("#message-personalclick").children().on("click", function() {
            $("#company-allname").addClass("d-none");
            $("#contactman-manage").children().attr("disabled", "disabled");
            $("#domain-owner").children().eq(0).attr("placeholder", "中文名，2-10个汉字").siblings().attr("placeholder", "英文名，3-20个字母")
        });
        $("#message-companyclick").children().on("click", function() {
            $("#company-allname").removeClass("d-none");
            $("#contactman-manage").children().removeAttr("disabled");
            $("#domain-owner").children().eq(0).attr("placeholder", "中文名，2-64个汉字").siblings().attr("placeholder", "英文名，3-128个字母")
        });
        $("#messagedemo-select").on("change", function() {
            if ($("option:selected", this).val() == "domainowner") {
                $("#messagedemo-find").attr("placeholder", "请输入所有者进行搜索");
            } else if ($("option:selected", this).val() == "contactman") {
                $("#messagedemo-find").attr("placeholder", "请输入联系人进行搜索");
            }
        })
    })
    //优惠资源包页面购买时间，到期时间
$(function() {
    var count = 0;
    $("#buyed-time").on("click", function() {
        count++;
        $("#expire-time").children().removeClass("d-none");
        if (count % 2) {
            $(this).children().eq(0).removeClass("d-none").siblings().addClass("d-none");
        } else {
            $(this).children().eq(0).addClass("d-none").siblings().removeClass("d-none");
        }
    });
    $("#expire-time").on("click", function() {
        count++;
        $("#buyed-time").children().removeClass("d-none");
        if (count % 2) {
            $(this).children().eq(0).removeClass("d-none").siblings().addClass("d-none");
        } else {
            $(this).children().eq(0).addClass("d-none").siblings().removeClass("d-none");
        }
    })
})
$(function() {
    var count = 0;
    $(".toggledemo").on("click", function() {
        count++;
        if (count % 2) {
            $(this).children().eq(0).removeClass("d-none").siblings().addClass("d-none");
        } else {
            $(this).children().eq(0).addClass("d-none").siblings().removeClass("d-none");
        }
    })
})

//dnsanalysis-server-buy购买dns解析服务页面
$(function() {
    $(window).scroll(function() {
        if ($(window).scrollTop() >= ($(".fixed-top").height() + $(".card-header").height() + 15)) {
            $("#domainscroll1").addClass("hostfixed");
            $("#domainscroll2").addClass("d-block");
        } else {
            $("#domainscroll1").removeClass("hostfixed");
            $("#domainscroll2").removeClass("d-block");
        };
        $("#domainscroll1").width($("#domainscroll2").width());
    })
});
$(function() {
    $("#adddomain1-radio").children().on("click", function() {
        $("#adddomain3").children().eq(0).removeClass("d-none").siblings().addClass("d-none");
    });
    $("#adddomain2-radio").children().on("click", function() {
        $("#adddomain3").children().eq(0).addClass("d-none").siblings().removeClass("d-none");
    })
});
$(function() {
        $("#domain-yearchoose").children().on("click", function() {
            $(this).removeClass("btn-light text-primary").addClass("btn-primary").siblings().removeClass("btn-primary").addClass("btn-light text-primary");
        })
    })
    //linegroup线路组页面
$(function() {
        $("#area-mainland").parent().children().on("click", function() {
            $("#operator").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#area-gangaotai").parent().children().on("click", function() {
            $("#chinese-city").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#area-foreign").parent().children().on("click", function() {
            $("#foreign-city").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#dianxin").parent().children().on("click", function() {
            $("#operator-dianxin").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#yidong").parent().children().on("click", function() {
            $("#operator-yidong").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#liantong").parent().children().on("click", function() {
            $("#operator-liantong").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#tietong").parent().children().on("click", function() {
            $("#operator-tietong").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#pengboshi").parent().children().on("click", function() {
            $("#operator-pengboshi").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#guangdian").parent().children().on("click", function() {
            $("#operator-guangdian").removeClass("d-none").siblings().addClass("d-none");
        });
        $("#huashu").parent().children().on("click", function() {
            $("#operator-huashu").removeClass("d-none").siblings().addClass("d-none");
        });
    })
    //点击删除后，弹出模态框询问(公用样式)
$(function() {
    $("#demo-tbody").on("click", ".deleteelement", function() {
        $(this).parent().parent().addClass("willdelete");
        $("#delete-sure").on("click", function() {
            $(".willdelete").remove();
        });
        $(this).parent().parent().siblings().removeClass("willdelete");
    })
});
//启用,暂停(公用样式)
$(function() {
    $(".start-stop").children().on("click", function() {
        $(this).addClass("d-none").siblings().removeClass("d-none");
    })
});

//dnsanalysis-server-add页面
$(function() {
    $("#mx-priority").prev().children().on("click", function() {
        var count = $("#mx-priority").val();
        if (count > 0) {
            count--;
            $("#mx-priority").val(count);
        }
    });
    $("#mx-priority").next().children().on("click", function() {
        var count = $("#mx-priority").val();
        if (count < 50) {
            count++;
            $("#mx-priority").val(count);
        }
    });
    $("#mx-priority").on("input", function() {
        var count = $("#mx-priority").val();
        if (count < 0) {
            $("#mx-priority").val(0);
        } else if (count > 50) {
            $("#mx-priority").val(50);
        }
    })
});
$(function() {
    $("#record-type").on("change", function() {
        if ($("option:selected", this).val() == "mx") {
            $("#mxPriority").removeClass("d-none");
            $("#isitIP2").parent().removeClass("d-none");
            $("#isitIP").parent().addClass("d-none")
        } else {
            $("#mxPriority").addClass("d-none");
            $("#isitIP2").parent().addClass("d-none");
            $("#isitIP").parent().removeClass("d-none");
        }
    })
})

//cloudhost-vps
$(function() {
    $("#sevendaysover").children().on("click", function() {
        $(this).addClass("d-none").siblings().removeClass("d-none");
    })
})

$(function() {
    $(".tag-style").on("mouseenter", function() {
        $(".tag-style2").addClass("d-block").children().eq(0).addClass("d-none");
    })
    $(".tag-style").on("mouseleave", function() {
        $(".tag-style2").removeClass("d-block");
    })
    $(".tag-style2").on("mouseenter", function() {
        $(this).addClass("d-block");
    }).children().on("click", function() {
        $(".tag-style2").removeClass("d-block");
    })
    $(".tag-style2").on("mouseleave", function() {
        $(this).removeClass("d-block");
    })
})
$(function() {
    $("#example-choose2").children().not(".tag-style,.tag-style2").on("click", function() {
        $("#example-choose1").text($(this).text());
        $("#example-input").attr("placeholder", "请输入" + $(this).text() + "进行搜索")
    })
    $(".tag-style2").children().on("click", function() {
        $("#example-choose1").text("标签/" + $(this).text());
        $("#example-input").attr("placeholder", "请输入标签值进行搜索")
    })
    $(".tag-style2").children().eq(2).on("click", function() {
        $("#example-input").attr("placeholder", "请输入标签进行搜索")
    })
})

//cloudhost-vps-create
//固定购买框 
$(function() {
    $(window).scroll(function() {
        if ($(window).scrollTop() >= ($(".fixed-top").height() + $(".card-header").height())) {
            $("#cloudhostscroll1").addClass("hostfixed");
        } else {
            $("#cloudhostscroll1").removeClass("hostfixed");
        };
    });
});
$(function() {
    $(".memoryselect").children().on("click", function() {
        $(this).parent().prev().children().eq(0).text($(this).text())
    })
})

//按钮点击选中效果
$(function() {
    $(".buttonclick-toggle").children().on("click", function() {
        $(this).removeClass("btn-light text-primary").addClass("btn-primary").siblings().removeClass("btn-primary").addClass("btn-light text-primary");
    })
})

//vps购买个数
$(function() {
    $("#vps-amount").prev().children().on("click", function() {
        var count = $("#vps-amount").val();
        if (count > 1) {
            count--;
            $("#vps-amount").val(count);
            $("#vps-maxamount").addClass("d-none");
        }
    });
    $("#vps-amount").next().children().on("click", function() {
        var count = $("#vps-amount").val();
        if (count < 99) {
            count++;
            $("#vps-amount").val(count);
        } else if (count == 99) {
            count++;
            $("#vps-amount").val(count);
            $("#vps-maxamount").removeClass("d-none");
        }
    });
    $("#vps-amount").on("input", function() {
        var count = $("#vps-amount").val();
        if (count == 100) {
            $("#vps-maxamount").removeClass("d-none");
        } else if (count > 100) {
            $("#vps-amount").val("100");
            $("#vps-maxamount").removeClass("d-none");
        } else if (count < 0) {
            $("#vps-amount").val("1");
        } else {
            $("#vps-maxamount").addClass("d-none");
        };
    })
});
$(function() {
    $(".user-custom1").on("click", function() {
        $(".user-custom2").removeClass("d-none");
    }).prev("button").on("click", function() {
        $(".user-custom2").addClass("d-none");
    })
})
$(function() {
    $("#random-generation1").on("click", function() {
        $("#random-generation2").removeClass("d-none");
        $("#user-defined2").addClass("d-none");
        $("#secretkey-double2").addClass("d-none");
    });
    $("#user-defined1").on("click", function() {
        $("#random-generation2").addClass("d-none");
        $("#user-defined2").removeClass("d-none");
        $("#secretkey-double2").addClass("d-none");
    });
    $("#secretkey-double1").on("click", function() {
        $("#random-generation2").addClass("d-none");
        $("#user-defined2").addClass("d-none");
        $("#secretkey-double2").removeClass("d-none");
    })
})
$(function() {
    $("#oldsecretkey-double").children().on("click", function() {
        $("#secretkey-input").removeClass("d-none");
        $("#secretkey-remind").addClass("d-none")
    });
    $("#newsecretkey-double").children().on("click", function() {
        $("#secretkey-input").addClass("d-none");
        $("#secretkey-remind").removeClass("d-none")
    })
})
$(function() {
    $(".dropdownmenu-secretkey").children().not(".dropdownitem-search").on("click", function() {
        $(".dropdownmenu-secretkey").prev().children().eq(0).text($(this).text());
    })
})
$(function() {
    $(".label-add2").on("click", ".labelbind-delete", function() {
        $(this).parent().remove();
    })
})
$(function() {
    $(".zlabel-add").on("click", function() {
        $('<div class="form-inline mb-2"><div class="form-inline"><span>标签键：</span><div class="input-group" data-toggle="dropdown"><div class="input-group-prepend">' +
            '<input type="text" class="form-control form-control-sm border-right-0 zfs-12 border zlabel-input1" style="width:200px" placeholder="选择已有或手动输入"></div><div class="input-group-append">' +
            '<button type="button" class="btn btn-light btn-sm border bg-transparent border-left-0 px-0"><span class="iconfont iconicon_jiantou-xia zfs-12 font-weight-bold text-secondary"></span></button>' +
            '</div></div><div class="dropdown-menu d-none py-0 zlabel-dropdown" style="width:221px"><a href="javascript:;" class="dropdown-item pl-2 linkblue">默认项目</a></div></div>' +
            '<div class="form-inline"><span class="ml-2">值：</span><div class="input-group" data-toggle="dropdown"><div class="input-group-prepend"><input type="text" class="form-control form-control-sm border-right-0 zfs-12 border zlabel-input2" style="width:200px" placeholder="选择已有或手动输入">' +
            '</div><div class="input-group-append"><button type="button" class="btn btn-light btn-sm border bg-transparent border-left-0 px-0"><span class="iconfont iconicon_jiantou-xia zfs-12 font-weight-bold text-secondary"></span>' +
            '</button></div></div><div class="dropdown-menu d-none py-0 zlabel-dropdown" style="width:221px"><a href="javascript:;" class="dropdown-item pl-2 linkblue">默认项目</a></div></div>' +
            '<button type="button" class="btn btn-light btn-sm bg-transparent border-0 pl-2 labelbind-delete"><span class="iconfont iconicon_guanbi font-weight-bold linkblue2"></span></button>' +
            '</div>').appendTo(".label-add2");
    });
});
$(function() {
    $(".zlabel-input1").on("input", function() {
        if ($(this).val().length == 0) {
            $(".labelinput-remind1").removeClass("d-none");
        } else {
            $(".labelinput-remind1").addClass("d-none");
        }
        if ($(this).val().length >= 65 || $(".zlabel-input2").val().length >= 65) {
            $(".labelinput-remind2").removeClass("d-none");
        } else {
            $(".labelinput-remind2").addClass("d-none");
        }
    })
    $(".zlabel-input2").on("input", function() {
        if ($(this).val().length >= 65 || $(".zlabel-input1").val().length >= 65) {
            $(".labelinput-remind2").removeClass("d-none");
        } else {
            $(".labelinput-remind2").addClass("d-none");
        }
    })
})
$(function() {
    $(".zlabel-dropdown").children().on("click", function() {
        $(this).parent().prev().find("input").val($(this).text());
    })
})
$(function() {
    $(".operating-system").children().on("click", function() {
        $(this).parent().prev().children().eq(0).text($(this).text());
        $(this).addClass("text-primary").siblings().removeClass("text-primary");
    })
})
$(function() {
    $(".operating-system1").children().eq(0).on("click", function() {
        $(this).parent().prev().children().eq(0).children().text($(this).text()).parent().addClass("iconicon_centos").removeClass("iconicon_ubuntu iconwindows1");
        $(this).addClass("text-primary").siblings().removeClass("text-primary");
    }).next().on("click", function() {
        $(this).parent().prev().children().eq(0).children().text($(this).text()).parent().addClass("iconicon_ubuntu").removeClass("iconicon_centos iconwindows1");
        $(this).addClass("text-primary").siblings().removeClass("text-primary");
    }).next().on("click", function() {
        $(this).parent().prev().children().eq(0).children().text($(this).text()).parent().addClass("iconwindows1").removeClass("iconicon_ubuntu iconicon_centos");
        $(this).addClass("text-primary").siblings().removeClass("text-primary");
    })
})
$(function() {
    $(".system-select").children().eq(0).on("click", function() {
        $(".centos-system").removeClass("d-none").nextAll().addClass("d-none");
    });
    $(".system-select").children().eq(1).on("click", function() {
        $(".ubuntu-system").removeClass("d-none").prev().addClass("d-none").next().next().addClass("d-none");
    });
    $(".system-select").children().eq(2).on("click", function() {
        $(".windows-system").removeClass("d-none").prev().addClass("d-none").prev().addClass("d-none");
    });
})
$(function() {
    $(".dropdownclick-toggle").children().on("click", function() {
        $(this).addClass("text-primary").siblings().removeClass("text-primary");
        $(this).parent().prev().children().eq(0).text($(this).text());
    })
})
$(function() {
    $("#huabei-beijing").on("click", function() {
        $(".huabei-beijing").removeClass("d-none").siblings().addClass("d-none");
    });
    $("#huabei-baoding").on("click", function() {
        $(".huabei-baoding").removeClass("d-none").siblings().addClass("d-none");
    });
    $("#huabei-guangzhou").on("click", function() {
        $(".huabei-guangzhou").removeClass("d-none").siblings().addClass("d-none");
    });
    $("#huabei-suzhou").on("click", function() {
        $(".huabei-suzhou").removeClass("d-none").siblings().addClass("d-none");
    });
    $("#huazhong-wuhan").on("click", function() {
        $(".huazhong-wuhan").removeClass("d-none").siblings().addClass("d-none");
    });
    $("#hongkong").on("click", function() {
        $(".hongkong").removeClass("d-none").siblings().addClass("d-none");
    });
})
$(function() {
    $(".common-type3").on("click", function() {
        $("#common-type3").removeClass("d-none").siblings().addClass("d-none");
    });
    $(".common-type2").on("click", function() {
        $("#common-type2").removeClass("d-none").siblings().addClass("d-none");
    });
    $(".common-type1").on("click", function() {
        $("#common-type1").removeClass("d-none").siblings().addClass("d-none");
    });
    $(".bettercount-type2").on("click", function() {
        $("#bettercount-type2").removeClass("d-none").siblings().addClass("d-none");
    });
    $(".bettercount-type1").on("click", function() {
        $("#bettercount-type1").removeClass("d-none").siblings().addClass("d-none");
    });
    $(".bettersave-type1").on("click", function() {
        $("#bettersave-type1").removeClass("d-none").siblings().addClass("d-none");
    });
    $(".gpu-type1").on("click", function() {
        $("#gpu-type1").removeClass("d-none").siblings().addClass("d-none");
    })
})
$(function() {
    $("#pay-now").on("click", function() {
        $(".pay-later").addClass("d-none");
        $(".pay-now").removeClass("d-none");
    });
    $("#pay-later").on("click", function() {
        $(".pay-later").removeClass("d-none");
        $(".pay-now").addClass("d-none");
    })
})
$(function() {
    $("#cost-on-year").on("click", function() {
        $(this).parent().siblings("span").text("");
        $("#rangeinput-count").children().eq(1).text("100Mbps").next().text("200Mbps");
        $("#rangeinput").attr("max", "200");
    })
    $("#cost-on-flow").on("click", function() {
        $(this).parent().siblings("span").text("后付费模式，按使用流量（单位为GB）计费，每小时扣费，请保证余额充足。");
        $("#rangeinput-count").children().eq(1).text("500Mbps").next().text("1000Mbps");
        $("#rangeinput").attr("max", "1000");
    });
    $("#cost-on-bandwidth").on("click", function() {
        $(this).parent().siblings("span").text("后付费模式，按分钟计费，每小时扣费，请保证余额充足。");
        $("#rangeinput-count").children().eq(1).text("100Mbps").next().text("200Mbps");
        $("#rangeinput").attr("max", "200");
    })
})
$(function() {
    $("#rangeinput").on("input", function() {
        $(this).siblings().children("input").val($(this).val());
    })
})
$(function() {
    $("#commonweb-buy").parent().children().on("click", function() {
        $("#commonweb-buy2").removeClass("d-none");
    });
    $("#commonweb-notbuy").parent().children().on("click", function() {
        $("#commonweb-buy2").addClass("d-none");
    })
})