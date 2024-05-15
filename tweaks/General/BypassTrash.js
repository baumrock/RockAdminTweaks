// Delete + non-superuser Trash actions
// console.log(ProcessWire.config.AOS_BypassTrash);
$(document).on("mousedown", "a.aos-pagelist-confirm", function (e) {
    var str_cancel = ProcessWire.config.AOS_BypassTrash.str_cancel;
    var str_confirm = ProcessWire.config.AOS_BypassTrash.str_confirm;

    // console.log(str_cancel);
    // console.log(str_confirm);

    e.preventDefault();
    if (e.which === 3 || e.which === 2) return false;

    var link = $(this),
        url = link.attr("href"),
        linkTextDefault;

    if (!link.attr("data-text-original")) {
        var currentText = $(this).get(0).childNodes[1]
            ? $(this).get(0).childNodes[1].nodeValue
            : $(this).html();
        link.attr("data-text-original", currentText);
        if (link.hasClass("PageListActionDelete") || link.hasClass("PageDelete")) {
            link.attr("data-text-confirm", str_confirm);
        }
    }

    if (url.indexOf("&force=1") === -1) {
        var linkCancel;
        linkTextDefault = link.attr("data-text-original");

        if (link.hasClass("cancel")) {
            linkCancel = link.next("a");
            linkCancel
                .removeClass("cancel")
                .attr("href", link.attr("href").replace("&force=1", ""))
                .contents()
                .last()[0].textContent = linkTextDefault;
            link.replaceWith(linkCancel);
            return false;
        }

        linkTextDefault = link.attr("data-text-confirm")
            ? link.attr("data-text-confirm")
            : link.attr("data-text-original");
        linkCancel = link.clone(true);
        linkCancel.addClass("cancel").contents().last()[0].textContent = " " + str_cancel;

        // replace text only (keep icon)
        link.contents().last()[0].textContent = linkTextDefault;
        link.attr("href", link.attr("href") + "&force=1");
        link.before(linkCancel);
    }

    return false;
});
