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