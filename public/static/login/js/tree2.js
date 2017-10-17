function tree(data, fitem, treeType, user) {
    if(user) {
        for(var i = 0; i < data.length; i++) {
            var data2 = data[i];
            if(data[i].fid == "0") {
                fitem.append("<li data-did='" + data[i].id + "' data-fid='" + data[i].fid + "' data-id='" + data[i].bmname + "' data-name='" + data[i].id + "'><span style='display: block;'><i class='caret total-clickss-join qiduo-first'></i> " + "<i class='groupwicon icon-wenjianjia'></i><span>" + data[i].bmname + "</span></span></li>");
            } else {
                var children = fitem.find("li[data-name='" + data[i].fid + "']").children("ul");
                if(children.length == false) {
                    fitem.find("li[data-name='" + data[i].fid + "']").append("<ul></ul>")
                }
                fitem.find("li[data-name='" + data[i].fid + "'] > ul").append(
                    "<li data-did='" + data[i].id + "' data-fid='" + data[i].fid + "' data-id='" + data[i].bmname + "' data-name='" + data[i].id + "' data-name='" + data[i].id + "'>" +
                    "<span style='display: block;'>" +
                    "<i class='caret total-clickss-join qiduo-first'></i> " +
                    "<i class='groupwicon icon-wenjianjia'></i><span>" +
                    data[i].bmname +
                    "</span></span>" +
                    "</li>")
            }
            for(var j = 0; j < data[i].child.length; j++) {
                var child = data[i].child[j];
                var children = fitem.find("li[data-name='" + child.fid + "']").children("ul");
                if(children.length == false) {
                    fitem.find("li[data-name='" + child.fid + "']").append("<ul></ul>")
                }
                fitem.find("li[data-name='" + child.fid + "'] > ul").append(
                    "<li data-did='" + child.id + "' data-fid='" + child.fid + "' data-id='" + child.bmname + "' data-name='" + child.id + "'>" +
                    "<span style='display: block;'>" +
                    "<i class='caret total-clickss-join qiduo-first'></i> " +
                    "<i class='groupwicon icon-wenjianjia'></i><span>" +
                    child.bmname +
                    "</span></span>" +
                    "</li>")
                var child2 = data[i].child[j].child;
                tree(child2, fitem, treeType, user);
            }
            tree(data[i], fitem, treeType, user);
        }
    } else {
        if(treeType == 'open') {
            for(var i = 0; i < data.length; i++) {
                var data2 = data[i];
                if(data[i].fid == "0") {
                    fitem.append("<li data-did='" + data[i].id + "' data-fid='" + data[i].fid + "' data-id='" + data[i].bmname + "' data-name='" + data[i].id + "'><span style='display: block;'><i class='caret total-clickss-join qiduo-first'></i> " + "<i class='groupwicon icon-wenjianjia'></i><span>" + data[i].bmname + "</span></span></li>");
                } else {
                    var children = fitem.find("li[data-name='" + data[i].fid + "']").children("ul");
                    if(children.length == false) {
                        fitem.find("li[data-name='" + data[i].fid + "']").append("<ul></ul>")
                    }
                    fitem.find("li[data-name='" + data[i].fid + "'] > ul").append(
                        "<li data-did='" + data[i].id + "' data-fid='" + data[i].fid + "' data-id='" + data[i].bmname + "' data-name='" + data[i].id + "' data-name='" + data[i].id + "'>" +
                        "<span style='display: block;'>" +
                        "<i class='caret total-clickss-join qiduo-first'></i> " +
                        "<i class='groupwicon icon-wenjianjia'></i><span>" +
                        data[i].bmname +
                        "</span></span>" +
                        "</li>")
                }
                for(var j = 0; j < data[i].child.length; j++) {
                    var child = data[i].child[j];
                    var children = fitem.find("li[data-name='" + child.fid + "']").children("ul");
                    if(children.length == false) {
                        fitem.find("li[data-name='" + child.fid + "']").append("<ul></ul>")
                    }
                    fitem.find("li[data-name='" + child.fid + "'] > ul").append(
                        "<li data-did='" + child.id + "' data-fid='" + child.fid + "' data-id='" + child.bmname + "' data-name='" + child.id + "'>" +
                        "<span style='display: block;'>" +
                        "<i class='caret total-clickss-join qiduo-first'></i> " +
                        "<i class='groupwicon icon-wenjianjia'></i><span>" +
                        child.bmname +
                        "</span></span>" +
                        "</li>")
                    var child2 = data[i].child[j].child;
                    tree(child2, fitem, treeType, '');
                }
                tree(data[i], fitem, treeType, '');
            }
        } else if(treeType == 'close') {
            for(var i = 0; i < data.length; i++) {
                var data2 = data[i];
                if(data[i].fid == "0") {
                    fitem.append("<li data-did='" + data[i].id + "' data-fid='" + data[i].fid + "' data-id='" + data[i].bmname + "' data-name='" + data[i].id + "'><span style='display: block;'><i class='caret total-clickss-join qiduo-first'></i> " + "<i class='groupwicon icon-wenjianjia'></i><span>" + data[i].bmname + "</span></span></li>");
                } else {
                    var children = $("li[data-name='" + data[i].fid + "']").children("ul");
                    if(children.length == false) {
                        fitem.find("li[data-name='" + data[i].fid + "']").append("<ul></ul>")
                    }
                    fitem.find("li[data-name='" + data[i].fid + "'] > ul").append(
                        "<li style='display: none;' data-did='" + data[i].id + "' data-fid='" + data[i].fid + "' data-id='" + data[i].bmname + "' data-name='" + data[i].id + "' data-name='" + data[i].id + "'>" +
                        "<span style='display: block;'>" +
                        "<i class='caret total-clickss-join qiduo-first'></i> " +
                        "<i class='groupwicon icon-wenjianjia'></i><span>" +
                        data[i].bmname +
                        "</span></span>" +
                        "</li>")
                }
                for(var j = 0; j < data[i].child.length; j++) {
                    var child = data[i].child[j];
                    var children = fitem.find("li[data-name='" + child.fid + "']").children("ul");
                    if(children.length == false) {
                        fitem.find("li[data-name='" + child.fid + "']").append("<ul></ul>")
                    }
                    fitem.find("li[data-name='" + child.fid + "'] > ul").append(
                        "<li style='display: none;' data-did='" + child.id + "' data-fid='" + child.fid + "' data-id='" + child.bmname + "' data-name='" + child.id + "'>" +
                        "<span style='display: block;'>" +
                        "<i class='caret total-clickss-join qiduo-first'></i> " +
                        "<i class='groupwicon icon-wenjianjia'></i><span>" +
                        child.bmname +
                        "</span></span>" +
                        "</li>")
                    var child2 = data[i].child[j].child;
                    tree(child2, fitem, treeType, '');
                }
                tree(data[i], fitem, treeType, '');
            }
        }
    }
}