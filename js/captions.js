const MAX_CHARS_PER_LINE = 60;

function setCaption(caption) {
    $(".caption-line").remove();
    
    while(caption.length > 0) {
        var substrTo = MAX_CHARS_PER_LINE;
        if(caption.length <= MAX_CHARS_PER_LINE)
            substrTo = caption.length;
        else {
            while(caption.charAt(substrTo) != ' ' && caption.charAt(substrTo) != '-' && caption.charAt(substrTo + 1) != ' ')
                substrTo--;
        }
        
        $("#video > .yt-video").append($("<span class='caption-line'>" + caption.substring(0, substrTo) + "</span>"));
        console.log("<span class='caption-line'>" + caption.substring(0, substrTo) + "</span>");
        caption = caption.substr(substrTo);
    }
}

setCaption("Sunless: Have you ever gone on Rocket League and just immediately dominated? Like, for whatever reason you just could not miss.");