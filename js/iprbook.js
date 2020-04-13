$(document).ready(function () {
    send_request(0, $("#id_iprbookid").val());
});

$("#iprbook-search").click(function () {
    send_request();
});

function send_request(page = 0, iprbookid = 0) {
    var filter = $(".ipr-filter"),
        title = $("#filter-title").val();

    $.ajax({
        url: M.cfg.wwwroot + "/mod/iprbook/ajax.php?action=getlist&page=" + page + "&title=" + title + "&iprbookid=" + iprbookid
    }).done(function (data) {
        clear_details();

        // set data
        $("#ipr-items-list").scrollTop(0);
        $("#ipr-items-list").html(data.html);

        // set details click listener
        $(".iprbook-select").click(function () {
            $(".ipr-item").removeClass("ipr-item-selected");
            set_details($(this).data("id"));
            $(this).parent().parent().parent().addClass("ipr-item-selected");
        });

        if (iprbookid > 0) {
            $('#ipr-items-details').html(data.details);
        }

        // pagination
        $(".ipr-page").click(function () {
            send_request($(this).data('page'));
        });
    });
}

function set_details(id) {
    var title = $("#ipr-item-title-" + id).html();

    $("#id_iprbookid").val(id);
    $("#id_name").val(title.substring(title.lastIndexOf(">") + 1));
    $("#ipr-item-detail-image").html($("#ipr-item-image-" + id).html());
    $("#ipr-item-detail-title").html(title);
    $("#ipr-item-detail-pubhouse").html($("#ipr-item-pubhouse-" + id).html());
    $("#ipr-item-detail-authors").html($("#ipr-item-authors-" + id).html());
    $("#ipr-item-detail-pubyear").html($("#ipr-item-pubyear-" + id).html());
    $("#ipr-item-detail-description").html($("#ipr-item-description-" + id).html());
    $("#ipr-item-detail-keywords").html($("#ipr-item-keywords-" + id).html());
    $("#ipr-item-detail-pubtype").html($("#ipr-item-pubtype-" + id).html());

    // var rb = $("#ipr-item-detail-read");
    // rb.attr("href", $("#ipr-item-url-" + id).attr("href"));
    // if ($("#ipr-item-url-" + id).attr("href")) {
    //     rb.show();
    // }
}

function clear_details() {
    $("#ipr-item-detail-image").html('');
    $("#ipr-item-detail-title").html('');
    $("#ipr-item-detail-pubhouse").html('');
    $("#ipr-item-detail-authors").html('');
    $("#ipr-item-detail-pubyear").html('');
    $("#ipr-item-detail-description").html('');
    $("#ipr-item-detail-keywords").html('');
    $("#ipr-item-detail-pubtype").html('');
}