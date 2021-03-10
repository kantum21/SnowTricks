//Edit main picture
$("input[type=radio][name='trick_form[mainPicture]']").change(function () {
    var id_input = $("input[name='trick_form[mainPicture]']:checked:enabled").attr('id');
    var labelElement = $("label[for='" + id_input + "']");
    var imageElement = labelElement.children();
    var src = imageElement.attr('src');
    $("#mainPicture").attr('src', src);
});

//Delete main picture
$("#mainPictureDeleteButton").click(function (e) {
    e.preventDefault();
    $("input[type=radio][name='trick_form[mainPicture]']").first().prop("checked", true);
    var id_input = $("input[name='trick_form[mainPicture]']:checked:enabled").attr('id');
    var labelElement = $("label[for='" + id_input + "']");
    var imageElement = labelElement.children();
    var src = imageElement.attr('src');
    $("#mainPicture").attr('src', src);
});