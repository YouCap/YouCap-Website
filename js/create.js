var vidLinkRegex = new RegExp("^http(?:s?):\\/\\/(?:www\\.)?youtu(?:be\\.com\\/watch\\?v=|\\.be\\/)([\\w\\-\\_]*)(&(amp;)?[\\w\\=]*)?$", "m");

$("#main-content form button[name=vid-link-button]").click(function() {
    var link = $("#main-content form input#vid-link").val();
    
    if(vidLinkRegex.test(link)) {
        var id = vidLinkRegex.exec(link)[1];
        $("#video > .yt-video:first-of-type > iframe").attr("src", "https://www.youtube.com/embed/" + id)
        $("form input[name=vid-id]").val(id);
    } else {
        alert("ERROR");
    }
});



$("form button.select").click(function() {
    $(this).css("background-color", "#7e7e7e");
    $(this).find("> div").css("display", "block");
}).blur(function() {
    $(this).css("background-color", "");
    $(this).find("> div").css("display", "none");
});

$("form button.select > div div").click(function(event) {
    event.stopPropagation();
    
    $(this).closest(".select").blur().find("> p").text($(this).text());
    $("form input[name=" + $(this).parent().parent().attr("name") + "]").val($(this).attr("value"));
    $("form input[name=" + $(this).parent().parent().attr("name") + "-name]").val($(this).text());
});