{
    $E(".sidebarchild").innerHTML = TEMPS.server_bcd();
    let href = window.location.href;
    href = href.split("/");
    let str = href[href.length - 1];
    for (let index in $E('.main-sidebar-t a')) {
        let h = $E('.main-sidebar-t a')[index].href;
        if (h !== undefined && h !== "javascript:;") {
            h = h.split("/");
            let f = h[h.length - 1];
            if (f == str && f.indexOf("#") == -1) {
                let box = $E('.main-sidebar-t a')[index];
                if (box.parentNode.parentNode.className == "sidebar-item-child") {
                    box.parentNode.parentNode.style.display = "block";
                }
                box.classList.add("border-left", "sidebar-border-left");
            }
        }
    }
} {
    $E(".sidebardns").innerHTML = TEMPS.server_dns();
    let href = window.location.href;
    href = href.split("/");
    let str = href[href.length - 1];
    for (let index in $E('.main-sidebar-t a')) {
        let h = $E('.main-sidebar-t a')[index].href;
        if (h !== undefined && h !== "javascript:;") {
            h = h.split("/");
            let f = h[h.length - 1];
            if (f == str && f.indexOf("#") == -1) {
                let box = $E('.main-sidebar-t a')[index];
                if (box.parentNode.parentNode.className == "sidebar-item-child") {
                    box.parentNode.parentNode.style.display = "block";
                }
                box.classList.add("border-left", "sidebar-border-left");
            }
        }
    }
} {
    $E(".sidebarssl").innerHTML = TEMPS.server_ssl();
    let href = window.location.href;
    href = href.split("/");
    let str = href[href.length - 1];
    for (let index in $E('.main-sidebar-t a')) {
        let h = $E('.main-sidebar-t a')[index].href;
        if (h !== undefined && h !== "javascript:;") {
            h = h.split("/");
            let f = h[h.length - 1];
            if (f == str && f.indexOf("#") == -1) {
                let box = $E('.main-sidebar-t a')[index];
                if (box.parentNode.parentNode.className == "sidebar-item-child") {
                    box.parentNode.parentNode.style.display = "block";
                }
                box.classList.add("border-left", "sidebar-border-left");
            }
        }
    }
} {
    $E(".sidebarvps").innerHTML = TEMPS.server_vps();
    let href = window.location.href;
    href = href.split("/");
    let str = href[href.length - 1];
    for (let index in $E('.main-sidebar-t a')) {
        let h = $E('.main-sidebar-t a')[index].href;
        if (h !== undefined && h !== "javascript:;") {
            h = h.split("/");
            let f = h[h.length - 1];
            if (f == str && f.indexOf("#") == -1) {
                let box = $E('.main-sidebar-t a')[index];
                if (box.parentNode.parentNode.className == "sidebar-item-child") {
                    box.parentNode.parentNode.style.display = "block";
                }
                box.classList.add("border-left", "sidebar-border-left");
            }
        }
    }
} {
    $E(".sidebarsms").innerHTML = TEMPS.server_sms();
    let href = window.location.href;
    href = href.split("/");
    let str = href[href.length - 1];
    for (let index in $E('.main-sidebar-t .sidebarsms a')) {
        let h = $E('.main-sidebar-t .sidebarsms a')[index].href;
        if (h !== undefined && h !== "javascript:;") {
            h = h.split("/");
            let f = h[h.length - 1];
            if (f == str && f.indexOf("#") == -1) {
                let box = $E('.main-sidebar-t .sidebarsms a')[index];
                if (box.parentNode.parentNode.className == "sidebar-item-child") {
                    box.parentNode.parentNode.style.display = "block";
                }
                box.classList.add("border-left", "sidebar-border-left");
            }
        }
    }
}