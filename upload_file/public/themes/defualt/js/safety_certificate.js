$E(".sidebarchild").innerHTML = TEMPS.safety_certificate();
let href = window.location.href;
href = href.split("/");
let str = href[href.length - 1];
for (let index in $E('a')) {
    let h = $E('a')[index].href;
    if (h !== undefined && h !== "javascript:;") {
        h = h.split("/");
        let f = h[h.length - 1];
        if (f == str && f.indexOf("#") == -1) {
            let box = $E('a')[index];
            if (box.parentNode.parentNode.className == "sidebar-item-child") {
                box.parentNode.parentNode.style.display = "block";
            }
            box.classList.add("border-left", "sidebar-border-left");
        }
    }
}

