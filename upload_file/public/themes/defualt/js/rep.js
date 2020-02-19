//邮箱验证
$("input.eamil").change(function() {
    if (RULE.isEmail(this.value)) {
        $(this).after(function() {
            $(this).siblings("span").html(" ");
            return `<span class="text-success">输入成功</span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).siblings("span").html(" ");
        $(this).after(function() {
            return `<span class="text-danger">邮箱输入有误</span>`
        })
    }
})

//身份id
$("input.idnumber").change(function() {
        if (RULE.IDnumber19(this.value)) {
            $(this).after(function() {
                $(this).siblings("span").html(" ");
                return `<span class="text-success">输入成功</span>`
            })
            $(this).removeClass("is-invalid")
            $(this).addClass("is-valid")
        } else {
            $(this).addClass("is-invalid")
            $(this).siblings("span").html(" ");
            $(this).after(function() {
                return `<span class="text-danger">只能为英文或数字,长度为8-19位</span>`
            })
        }
    })
    //地址中文
$("textarea.textc").change(function() {
        if (RULE.Cn40(this.value)) {
            $(this).after(function() {
                $(this).siblings("span").html(" ");
                return `<span class="text-success"></span>`
            })
            $(this).removeClass("is-invalid")
            $(this).addClass("is-valid")
        } else {
            $(this).addClass("is-invalid")
            $(this).siblings("span").html(" ");
            $(this).after(function() {
                return `<span class="text-danger">请输入合法的中文字符</span>`
            })
        }
    })
    //地址拼音
$("textarea.textc2").change(function() {
    if (RULE.En200(this.value)) {
        $(this).after(function() {
            $(this).siblings("span").html(" ");
            return `<span class="text-success"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).siblings("span").html(" ");
        $(this).after(function() {
            return `<span class="text-danger">请输入合法的英文字符</span>`
        })
    }
})

//邮编
$("input.postalCode").change(function() {
    if (RULE.pcode(this.value)) {
        $(this).after(function() {
            $(this).siblings("span").html(" ");
            return `<span class="text-success"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).siblings("span").html(" ");
        $(this).after(function() {
            return `<span class="text-danger">请输入正确格式的邮政编码</span>`
        })
    }
})

//手机号验证
$("input.tel").change(function() {
    if (RULE.isMobile(this.value)) {
        $(this).after(function() {
            $(this).siblings("span").html(" ");
            return `<span class="text-success"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).siblings("span").html(" ");
        $(this).after(function() {
            return `<span class="text-danger">请输入正确的手机号</span>`
        })
    }
})


//座机区号验证
$("input.zone").change(function() {
        if (RULE.zone(this.value)) {
            $(this).after(function() {
                $(this).siblings("span").html(" ");
                return `<span class="text-success"></span>`
            })
            $(this).removeClass("is-invalid")
            $(this).addClass("is-valid")
        } else {
            $(this).addClass("is-invalid")
            $(this).siblings("span").html(" ");
            $(this).after(function() {
                return `<span class="text-danger">区号错误</span>`
            })
        }
    })
    //座机号验证
$("input.zonetel").change(function() {
    if (RULE.zonenub(this.value)) {
        $(this).after(function() {
            $(this).siblings("span").html(" ");
            return `<span class="text-success"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).siblings("span").html(" ");
        $(this).after(function() {
            return `<span class="text-danger">请输入正确的座机号码</span>`
        })
    }
})

//名字中文
$("input.textc").change(function() {
        if (RULE.Cn40(this.value)) {
            $(this).parent().after(function() {
                $(this).siblings("span.spantext1").html(" ");
                return `<span class="text-success spantext1"></span>`
            })
            $(this).removeClass("is-invalid")
            $(this).addClass("is-valid")
        } else {
            $(this).addClass("is-invalid")
            $(this).parent().after(function() {
                $(this).siblings("span.spantext1").html(" ");
                return `<span class="text-danger form-inline spantext1">请输入不小于2个合法的中文字符</span>`
            })
        }
    })
    //名字拼音
$("input.textc2").change(function() {
    if (RULE.En200(this.value)) {
        $(this).parent().after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-success spantext"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")

        $(this).parent().after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-danger form-inline spantext">英文所有者只能包含字母,不小于3个字符</span>`
        })
    }
})

//解压密码
$("button.zip").click(function() {
    //四位数字
    let Rexp = /^\d{4}$/;
    if (Rexp.test($("input.zip").val())) {
        $("input.zip").after(function() {
            $("input.zip").siblings("span.spantext").html(" ");
            return `<span class="text-success spantext"></span>`
        })
        $("input.zip").removeClass("is-invalid")
        $("input.zip").addClass("is-valid")
    } else {
        $("input.zip").addClass("is-invalid")

        $("input.zip").after(function() {
            $("input.zip").siblings("span.spantext").html(" ");
            return `<span class="text-danger form-inline spantext">解压密码为4位数字,且不能为空</span>`
        })
    }
})

//域名验证
$("input.www").change(function() {
    let Rexp = /[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?$/;
    if (Rexp.test($(this).val())) {
        $(this).after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-success spantext"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-danger form-inline spantext">域名格式不符合要求</span>`
        })
    }
})


//两个字符
$("input.code2").change(function() {
    let Rexp = /^[a-zA-Z0-9]{2,}$/;
    if (Rexp.test($(this).val())) {
        $(this).after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-success spantext"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-danger form-inline spantext">签名仅支持中文、英文、数字且不能少于两个字符</span>`
        })
    }
})

//32个字符
$("input.code32").change(function() {
    let Rexp = /^.{1,32}$/;
    if (Rexp.test($(this).val())) {
        $(this).after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-success spantext"></span>`
        })
        $(this).removeClass("is-invalid")
        $(this).addClass("is-valid")
    } else {
        $(this).addClass("is-invalid")
        $(this).after(function() {
            $(this).siblings("span.spantext").html(" ");
            return `<span class="text-danger form-inline spantext">模板名称字符在32个以内且不能为空</span>`
        })
    }
})