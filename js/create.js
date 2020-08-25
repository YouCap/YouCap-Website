var vidLinkRegex = new RegExp("^http(?:s?):\\/\\/(?:www\\.)?youtu(?:be\\.com\\/watch\\?v=|\\.be\\/)([\\w\\-\\_]*)(&(amp;)?[\\w\\=]*)?$", "m");

$("form input[type=text]").keydown(function() {
    $(this).next("p.error-message").remove();
});

$("#main-content form button[name=vid-link-button]").click(function() {
    var link = $("#main-content form input.vid-link").val();
    
    if(vidLinkRegex.test(link)) {
        var id = vidLinkRegex.exec(link)[1];
        $("#video > .yt-video:first-of-type > iframe").attr("src", "https://www.youtube.com/embed/" + id)
        $("form input[name=vid-id]").val(id);
        $("form input.vid-link + p.error-message").remove();
    } else {
        if($("form input.vid-link + p.error-message").length <= 0)
            $("<p class='error-message'>Invalid YouTube link</p>").insertAfter($("form input[name=vid-link]"));
    }
});


function checkCreateForm() {
    var error = false;
    
    var link = $("#main-content form input.vid-link").val();   
    if(!vidLinkRegex.test(link)) {
        if($("form input.vid-link + p.error-message").length <= 0)
            $("<p class='error-message'>Invalid YouTube link</p>").insertAfter($("form input.vid-link"));
        error = true;
    }
    
    if($("form input[name=vid-lang]").val() == "") {
        if($("form button[name=vid-lang] + p.error-message").length <= 0)
            $("<p class='error-message'>Please select a language to make subtitles for</p>").insertAfter($("form button[name=vid-lang]"));
        error = true;
    }
    
    if(error) {
        var submitButton = $("form button[type=submit]");
        submitButton.addClass("error");
        setTimeout(function() {
            submitButton.removeClass("error");
        }, 500);
    }
    
    return !error;
}

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

if(findGetParameter("vid-id") != null) {
    $("form input.vid-link").val("https://www.youtube.com/watch?v=" + findGetParameter("vid-id"));
    $("form button[name=vid-link-button]").click();
}