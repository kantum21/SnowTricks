$(function () {
    $('.seemedias').click(function (e) {
        $('.seemedias').toggle();
        var styles = {
            "display": "flex",
            "flex-direction": "row",
            "flex-wrap": "wrap",
            "justify-content": "space-around"
        };
        $('.media-container').css(styles);
    });
});