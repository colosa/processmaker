navHover = function() {
    if (document.getElementById("dropdownMenu") != null) {
        var lis = document.getElementById("dropdownMenu").getElementsByTagName("LI");
        for (var i=0; i<lis.length; i++) {
            lis[i].onmouseover=function() {
                this.className+=" ieHover";
            }
            lis[i].onmouseout=function() {
                this.className=this.className.replace(new RegExp(" ieHover\\b"), "");
            }
        }
    }
}
if (window.attachEvent) window.attachEvent("onload", navHover);