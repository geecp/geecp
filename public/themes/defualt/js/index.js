window.onload = function() {
    $('[data-toggle="popover"]').popover();
    //sidebar init
    $('.sidebar-item:not(.border-t)').click(function(e) {
        // $('.sidebar-ul > li > .sidebar-item').removeClass('active');
        dropSwift($(this), '.sidebar-item-child');
        e.stopPropagation();
    });

    function dropSwift(dom, drop) {
        dom.next().slideToggle();
        dom.parent().siblings().find('div.sidebar-item').removeClass('active');
        dom.parent().siblings().find('.icon-icon_jiantou-you').removeClass('iconRotate');
        dom.parent().siblings().find(drop).slideUp();
        var iconChevron = dom.find('.icon-icon_jiantou-you');
        if (iconChevron.hasClass('iconRotate')) {
            iconChevron.removeClass('iconRotate');
            dom.removeClass('active');
        } else {
            iconChevron.addClass('iconRotate');
            dom.addClass('active');
        }
    }
}



const DATAPICKERAPI = {
    activeMonthRange: function() {
        return {
            begin: moment().set({
                'date': 1,
                'hour': 0,
                'minute': 0,
                'second': 0
            }).format('YYYY-MM-DD HH:mm:ss'),
            end: moment().set({
                'hour': 23,
                'minute': 59,
                'second': 59
            }).format('YYYY-MM-DD HH:mm:ss')
        }
    },
    shortcutMonth: function() {
        // 当月
        var nowDay = moment().get('date');
        var prevMonthFirstDay = moment().subtract(1, 'months').set({
            'date': 1
        });
        var prevMonthDay = moment().diff(prevMonthFirstDay, 'days');
        return {
            now: '-' + nowDay + ',0',
            prev: '-' + prevMonthDay + ',-' + nowDay
        }
    },
    // 注意为函数：快捷选项option:只能同一个月份内的
    rangeMonthShortcutOption1: function() {
        var result = DATAPICKERAPI.shortcutMonth();
        return [{
            name: '昨天',
            day: '-1,-1',
            time: '00:00:00,23:59:59'
        }, {
            name: '这一月',
            day: result.now,
            time: '00:00:00,'
        }, {
            name: '上一月',
            day: result.prev,
            time: '00:00:00,23:59:59'
        }];
    },
    // 快捷选项option
    rangeShortcutOption1: [{
        name: '最近一周',
        day: '-7,0'
    }, {
        name: '最近一个月',
        day: '-30,0'
    }, {
        name: '最近三个月',
        day: '-90, 0'
    }],
    singleShortcutOptions1: [{
        name: '今天',
        day: '0'
    }, {
        name: '昨天',
        day: '-1',
        time: '00:00:00'
    }, {
        name: '一周前',
        day: '-7'
    }]
};
//计时器 button
var sendCode = {
    init: function(t, n) {
        sendCode.times(t, n);
    },
    times: function(t, n) {
        var time = n;
        if (time == 0) {
            t.removeClass('hidden');
            t.next().remove();
            time = n;
        } else {
            if (t.next().length) {
                t.next().text(time + 's');
            } else {
                t.addClass('hidden');
                t.after('<button class="btn btn-ces btn-sm" type="button" style="background-color: #F03;" disabled="disabled">' + time + 's</button>');
            }
            time--;
            setTimeout(function() {
                sendCode.times(t, time);
            }, 1000)
        }
    }
};

//总览相关操作
$('.sidebar .allSee').mouseover(function() {
    $(this).addClass('on');
});
$('.sidebar .allSee').on('click', '.sidebar .allSee .close, .sidebar .allSee [data-proid] > a', function() {
    $('.sidebar .allSee').removeClass('on');
});
$('.sidebar .allSee').mouseout(function() {
    $('.sidebar .allSee').removeClass('on');
});


//全选操作
$(document).on('change', 'thead [type="checkbox"]', function() {
    if ($(this).is(':checked')) {
        $(this).parents('.table').find('[type="checkbox"]').prop('checked', true);
    } else {
        $(this).parents('.table').find('[type="checkbox"]').prop('checked', false);
    }
});
$(document).on('change', 'tbody [type="checkbox"]', function() {
    if ($(this).parents('tbody').find('[type="checkbox"]:checked').length == $(this).parents('tbody').find('[type="checkbox"]').length) {
        $('thead [type="checkbox"]').prop('checked', true);
    } else {
        $('thead [type="checkbox"]').prop('checked', false);
    }
    if ($(this).parents('tbody').find('[type="checkbox"]:checked').length >= 1) {
        $('.isChoose').prop('disabled', false);
    } else {
        $('.isChoose').prop('disabled', true);
    }
});

//通用range操作
$(document).on('change', '.range-group [type="range"]', function() {
    $(this).parents('.range-group').find('[type="text"]').val($(this).val() + $(this).data('unit'));
});
$(document).on('change', '.range-group [type="text"]', function() {
    let range = $(this).parents('.range-group').find('[type="range"]'),
        tVal = Number($(this).val() ? $(this).val().replace(/[^0-9]/ig, "") : '0');
    if (tVal > Number(range.attr('max'))) {
        range.val(range.attr('max'));
        $(this).val(range.attr('max') + range.data('unit'));
        return false;
    }
    if (tVal && tVal > Number(range.attr('min'))) {
        range.val(tVal);
        $(this).val(tVal + range.data('unit'))
    } else {
        range.val('5');
        $(this).val('5' + range.data('unit'))
    }
    $(this).parent().prev().find('[type="range"]').trigger('change');
});

//购买台数设置
$(document).on('click', '.buy-group .btn', function() {
    let t = $(this),
        father = $(this).parents('.buy-group'),
        item = father.find('input'),
        num = Number(item.val());
    if (t.hasClass('add')) {
        item.val(num + 1);
    } else {
        item.val(num - 1);
    }
    item.trigger('change');

});
$(document).on('change', '.buy-group input', function() {
    let t = $(this),
        father = $(this).parents('.buy-group'),
        item = father.find('input'),
        num = Number(item.val()),
        minnum = Number(item.data('min')),
        maxnum = Number(item.data('max'));
    if (num - 1 >= minnum) {
        father.find('.sub').prop('disabled', false)
    } else {
        father.find('.sub').prop('disabled', true)
        t.val(minnum);
    }
    if (maxnum != 0) {
        if (num + 1 <= maxnum) {
            father.find('.add').prop('disabled', false)
        } else {
            father.find('.add').prop('disabled', true)
            t.val(maxnum);
        }
    } else {
        father.find('.add').prop('disabled', false)
    }
});

$(function() {
    /*  
     * @name datePicker
     * @param {String} format	YYYY-MM-DD HH:mm:ss	datepicker 类型
     * @param {Boolean} isRange	false	是否范围选择	
     * @param {false/String} min	false	时间最小值	
     * @param {false/String} max	false	时间最大值	
     * @param {Boolean} hasShortcut	false	是否开启快捷选项	
     * @param {Array} shortcutOptions	[]	快捷选项配置参数	
     * @param {false/Number} between	false	开始和结束值之间的时间间隔天数（只对范围有效）	
     */
    $('.J-datepicker-range-day:not(".lefts")').datePicker({
        hasShortcut: false,
        format: 'YYYY-MM-DD',
        isRange: true,
        shortcutOptions: [],
        hide: (e) => {
            $('[name="starttime"],[name="endtime"]').trigger('change');
            // console.log(window.cbdate)
            datepickercb(window.cbdate)
        }
    });
    $('.J-datepicker-range-day.lefts').datePicker({
        hasShortcut: false,
        format: 'YYYY-MM-DD',
        isRange: true,
        shortcutOptions: [],
        hide: (e) => {
            $('[name="starttime"],[name="endtime"]').trigger('change');
            // console.log(window.cbdate)
            datepickercb(window.cbdate)
        }
    });
    function datepickercb(cb){
      cb && cb();
    }
    $('#address').on('show.bs.modal', function() {
        $('#goods').modal('hide');
    });
});