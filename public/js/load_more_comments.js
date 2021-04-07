$(function () {
    $("#load-more").click(function (e) {
        e.preventDefault();
        $(".load-more-btn").hide();
        var link = $(e.currentTarget);
        $.ajax({
            method: 'POST',
            url: link.attr('href')
        }).done(function (data) {
            $(".comments_container").append(data);
        })
    });
});