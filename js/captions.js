//Sets the currently displayed caption on the video embed.
function setCaption(caption, temporary) {    
    //If the caption was entered through the editing textarea, it's a preview and not a real caption.
    
    if(!temporary)
        $(".editor-captions .caption-line").remove();
    
    if(caption != "" && caption != undefined) {        
        if(temporary) {
            $(".editor-captions .caption-line").remove();
            $("#video > .yt-video .editor-captions").addClass("temporary");
        }
        else {
            $("#video > .yt-video .editor-captions").removeClass("temporary");
        }
        
        $("#video > .yt-video .editor-captions").append($("<span class='caption-line' dir='ltr'>" + caption + "</span>"));
    }
}

//setCaption("Sunless: Have you ever gone on Rocket League and just immediately dominated? Like, for whatever reason you just could not miss.");






//Utils
//Formats an entered value into a string in the format of MM:SS.MS
function timeFormat(value) { 
    if(value.match("^([0-9]*:)?[0-9]{2}:[0-9]{2}\\.[0-9]+$"))
        return value;
    
    var steps = ["[0-9]", "[0-9]", ":", "[0-9]", "[0-9]", ":", "[0-9]", "[0-9]", "\\.", "[0-9]+", ""];
    var placeholders = "00:00.0";
    
    var match = "";
    for(var i = steps.length - 1; i >= 0; i--) {
        match = steps[i] + match;
        if(value.match("^" + match + "$"))
            return placeholders.substring(0, i) + value.substring(0, steps.length - 1 - i);
    }
    
    return "";
    console.log("REGEX ERROR");
}

//Properly sets the start and end times when first adding a caption object.
function setTimes(captionObj) {
    var start = 0;
    var end = 0.5;
    
    var start1 = -1;
    var end1 = -1;
    var start2 = -1;
    var end2 = -1;
    
    //Get the start and end times of the previous caption, if it exists
    if(captionObj.prev(".caption").length > 0) {
        start1 = timeToSeconds(captionObj.prev(".caption").find(".times .start-time").val());
        end1 = timeToSeconds(captionObj.prev(".caption").find(".times .end-time").val());
    }
    //Get the start and end times of the following caption, if it exists
    if(captionObj.next(".caption").length > 0) {
        start2 = timeToSeconds(captionObj.next(".caption").find(".times .start-time").val());
        end2 = timeToSeconds(captionObj.next(".caption").find(".times .end-time").val());
    }
    
    //If the difference between the next and prior captions is greater than half a second, there's enough space for an additional caption.
    var diff = start2 - end1;
    
    if(start2 == -1)
        diff = player.getDuration() - end1;
    
    if(end1 != -1 && diff >= 0.5) {
        start = end1;
        //The caption will start initially at a length between 0.5 and 5 seconds.
        end = start + clamp(diff, 0.5, 5);
    } 
    //If the difference is smaller, either the next or prior caption (or both) will have to be made less long to create space for a new caption
    else {
        //If the previous caption is more than 1 second long, it can make room for a 0.5-second long caption while still being above 0.5 seconds itself.
        if(end1 != -1 && end1 - start1 >= 1) {
            end1 -= 0.5;
            start = end1;
            end = start + 0.5;
        }
        //If the next caption is more than 1 second long, it can make room for a 0.5-second long caption while still being above 0.5 seconds itself.
        else if(end2 != -1 && end2 - start2 >= 1) {
            start2 += 0.5;
            end = start2;
            start = end - 0.5;
        }
        //If neither caption can make enough room on it's own, but they have a combined length of more than 1.5 seconds, they can both be compressed to make room for a 0.5-second long caption while remaining 0.5 seconds long (or greater) themselves.
        else if(end1 != -1 && end2 != -1 && (end1 - start1) + (end2 - start2) >= 1.5) {
            var tmpEnd1 = end1;
            end1 = clamp(end1 - 0.5, start1 + 0.5, end1);
            var endDiff = tmpEnd1 - end1;
            start2 += 0.5 - endDiff;
            start = end1;
            end = start2;
        }
        //If there is no prior caption, but there is one after, this caption is being made the first caption in the list
        else if(end1 == -1 && end2 != -1) {
            //If the next caption starts after 0.5 seconds, this caption can simply fill that void
            if(start2 >= 0.5) {
                start = 0;
                end = 0.5;
            }
            //If the next caption ends after 1 second, the next caption can be compressed to make a 0.5 second gap.
            else if(end2 >= 1) {
                start = 0;
                end = 0.5;
                start2 = 0.5;
            }
            //Otherwise, there's not enough space and the caption can't be created
            else {
//                console.log("ERROR");
                captionObj.remove();
                return [-1, -1];
            }
        }
        //If there is no next caption, but there is one prior, this caption is being made the last caption in the list
        else if(end1 != -1 && end2 == -1) {
            //If the end of the prior caption is at least 0.5 seconds from the end of the video, there's room for another caption.
            if(end1 + 0.5 <= player.getDuration()) {
                start = end1;
                end = start + 0.5;
            }
            //Otherwise, there's not enough room and the caption can't be created.
            else {
//                console.log("ERROR");
                captionObj.remove();
                return [-1, -1];
            }
        }
        //If the caption is being placed between two others, but they don't meet any of the prior conditions, there's not enough room for a new caption and the caption can't be created.
        else if(end1 != -1 && end2 != -1) {
//            console.log("ERROR");
            captionObj.remove();
            return [-1, -1];
        }
    }
    
    //Reapply the start and end times for the prior and following caption in case any modifications were made.
    if(start1 != -1) {
        captionObj.prev(".caption").find(".times .start-time").val(secondsToTime(start1));
        captionObj.prev(".caption").find(".times .end-time").val(secondsToTime(end1));
    }
    if(start2 != -1) {
        captionObj.next(".caption").find(".times .start-time").val(secondsToTime(start2));
        captionObj.next(".caption").find(".times .end-time").val(secondsToTime(end2));
    }
    
    //Apply the initial start and end times to the new caption.
    captionObj.find(".times .start-time").val(secondsToTime(start));
    captionObj.find(".times .end-time").val(secondsToTime(end));
    
    return [secondsToTime(start), secondsToTime(end)];
}

//Keeps a value clamped between two other values
function clamp(value, min, max) {
    if(value > max)
        return max;
    if(value < min)
        return min;
    return value;
}