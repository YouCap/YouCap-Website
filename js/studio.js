//Load Youtube Iframe API
var tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

//Create video player
var player;
window.onYouTubeIframeAPIReady = function() {
    player = new YT.Player('player', {
        height: '390',
        width: '640',
        host: 'https://www.youtube-nocookie.com',
        videoId: vidID,
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChanged
        }
    });
}

//Callbacks
//The current time in number of ticks.
var currTime = 0;

//The current zoom level of the timeline.
var currZoom = 0;

//The number of seconds each tick marks is worth per zoom level.
const zooms = [0.2, 1, 1, 1, 5, 5, 25, 25];

//The number of tick marks to show in the timeline at once per zoom level.
const ticks = [60, 23, 46, 92, 37, 74, 60, 120];

//The number of ticks between each time marking in the timeline per zoom level.
const marks = [5, 5, 5, 5, 3, 6, 4, 8];

//The amount of space betweem each tick on the timeline.
var tickSep = $(".waveform").width()/ticks[currZoom];
const SCROLL_BAR_WIDTH = 24;

function onPlayerReady() {
    $(".title").text(player.getVideoData()["title"]);
    setZoomLevel(0);
    
    drawTicks($(".waveform canvas")[0], 0);
    
    $(".waveform .playhead").draggable({
        axis: 'x',
        containment: 'parent'
    });
    $(".waveform .caption-boxes").draggable({
        axis: 'x',       
        drag: function(event, ui){ 
            //Stops from going too far to the left (below 00:00)
            ui.position.left = Math.min(ui.position.left, 0);
            
            //Stops from going too far to the right (above the video length)
            ui.position.left = Math.max(ui.position.left, ((player.getDuration()/zooms[currZoom]) * -tickSep) + $(".waveform").width());
            
            //Adjust the scrollbar position
            
            updateScrollbar(ui.position.left);
            
            //Set the current time
            currTime = Math.ceil(-ui.position.left/tickSep);
            
            //Update the drawn tick marks
            drawTicks($(".waveform canvas")[0], currZoom);
            
            playheadUpdated(false);
        },
        cancel: ".caption-box"
    });
    $(".waveform .scroll .scrollbar").draggable({
        axis: 'x',
        containment: 'parent',
        drag: function(event, ui) {
            //Get the ratio between the scrollbar and its container & the timeline and its container.
            var ratio = -ui.position.left/($(".waveform .scroll").width() - SCROLL_BAR_WIDTH);
            
            //Error adjustments for slight differences.
            if(ratio < -0.99)
                ratio = -1;
            else if(ratio > 0)
                ratio = 0;
            
            //Set the timeline's left position based on that ratio
            var tmpLeft = $(".waveform .caption-boxes").position().left;
            $(".waveform .caption-boxes").css("left", ratio * ($(".waveform .caption-boxes").width() - $(".waveform").width()))
            
            var leftDiff = $(".waveform .caption-boxes").position().left - tmpLeft;
            $(".waveform .caption-box").each(function() {
                //$(this).css("left", $(this).position().left + leftDiff);
            });
            
            //Set the current time based on the position of the timeline
            currTime = Math.ceil(-$(".waveform .caption-boxes").position().left/tickSep);
            
            drawTicks($(".waveform canvas")[0], currZoom);
            
            playheadUpdated(false);
        }
    });
}



//Caption Boxes
var oldMousePosX = 0;
var oldLeft = 0;
var oldWidth = 0;
var minLeft = 0;
var maxRight = 0;
var mouseDown = false;
$(document).on({
    mousedown: function(event) {
        oldMousePosX = event.pageX;
        oldLeft = $(this).parent().position().left;
        oldWidth = $(this).parent().width();
        maxRight = getMaxRight($(this).closest(".caption-box"));
        
        mouseDown = true;
        $(this).addClass("dragging");
        
        $("#current-time-label").removeClass("left").addClass("right");

        event.stopPropagation();
    }
}, ".caption-box .right-handle");
$(document).on({
    mousedown: function(event) {
        oldMousePosX = event.pageX;
        oldLeft = $(this).parent().position().left;
        oldWidth = $(this).parent().width();
        minLeft = getMinLeft($(this).closest(".caption-box"));
        
        mouseDown = true;
        $(this).addClass("dragging");
        
        $("#current-time-label").removeClass("right").addClass("left");

        event.stopPropagation();
    }
}, ".caption-box .left-handle");
$(document).on({
    mousedown: function(event) {
        oldMousePosX = event.pageX;
        oldLeft = $(this).position().left;
        oldWidth = $(this).width();
        minLeft = getMinLeft($(this));
        maxRight = getMaxRight($(this));
        
        mouseDown = true;
        $(this).addClass("dragging");
        
        $("#current-time-label").removeClass("right").removeClass("left");

        
        $(".caption-list .caption.selected").removeClass("selected");
        
        $(".caption-list .caption[data-caption-id='" + $(this).attr("data-caption-id") + "']").addClass("selected");
        
        $(".caption-list").animate({
            scrollTop: $(".caption[data-caption-id='" + $(this).attr("data-caption-id") + "']").position().top
        }, 2000);
        
        event.stopPropagation();
    },
    mouseenter: function() {
        if($(".caption-box.selected").length <= 0)
            $(this).addClass("selected");
    },
    mouseleave: function() {
        if($(this).find(".dragging").length <= 0 && !mouseDown)
            $(this).removeClass("selected");
    }
}, ".caption-box");

$("body").mousemove(function(event) {
    if(mouseDown) {
        $("#video iframe").css("pointer-events", "none");       
        
        //Get the x-position change
        var diff = event.pageX - oldMousePosX;
        
        //Find what handles are being dragged
        var right = $(".caption-box .right-handle.dragging").length > 0;
        var left = $(".caption-box .left-handle.dragging").length > 0;
        
        //Tracks the left position of the current time popup label
        var popupLeft = 0;
        var popupMargin = -10;
        var popupTime = "";
        
        if(right) {
            var el = $(".caption-box .right-handle.dragging");
                        
            //Clamp the end time for a caption between half a second from start and the start time of the next caption.
            var newWidth = clamp(oldWidth + diff, timeToPosition(0.5), maxRight - el.closest(".caption-box").position().left);
            
            //125 pixels from the right border of the caption (lining it up with said border)
            var newPopupLeft = oldLeft + $(".caption-boxes").position().left + (newWidth - 125);
            var clampedPopupLeft = clamp(newPopupLeft, 0, $(".waveform").width());
            popupLeft = clampedPopupLeft;
            popupMargin = newPopupLeft - clampedPopupLeft - 10;
            popupTime = timeFormat(secondsToTime(positionToTime(oldLeft + newWidth)));
            
            el.parent().width(newWidth);
        } else if(left) {
            var el = $(".caption-box .left-handle.dragging");
            
            //Clamp the start time between the end of the previous caption and half a second from the end of the video.
            var newLeft = clamp(oldLeft + diff, minLeft, oldLeft + oldWidth - timeToPosition(0.5));
            
            //Clamp the end time between half a second from the start of the video and the start time of the next video.
            var newWidth = clamp(oldWidth - diff, timeToPosition(0.5), oldLeft + oldWidth - minLeft);
            
            //Lines up at the left border of the caption.
            var newPopupLeft = newLeft + $(".caption-boxes").position().left;
            var clampedPopupLeft = clamp(newPopupLeft, 0, $(".waveform").width() - 125);
            popupLeft = clampedPopupLeft;
            popupMargin = clampedPopupLeft - newPopupLeft - 10;
            popupTime = timeFormat(secondsToTime(positionToTime(newLeft)));
            
            el.parent().css("left", newLeft);
            el.parent().width(newWidth);
        } else {
            var el = $(".caption-box.dragging");

            //Clamp the start time between the end of the video 
            var maxLeft = maxRight - el.width();
            var newLeft = clamp(oldLeft + diff, minLeft, maxLeft);
            
            //Directly in the center of the caption.
            var newPopupLeft = newLeft + $(".caption-boxes").position().left + (oldWidth/2) - (125/2);
            var clampedPopupLeft = clamp(newPopupLeft, 0, $(".waveform").width() - 125);
            popupLeft = clampedPopupLeft;
            popupMargin = newPopupLeft - clampedPopupLeft - 10;
            popupTime = timeFormat(secondsToTime(positionToTime(newLeft)));
            
            el.css("left", newLeft);
        }
        
        //Move the current time popup and update its time
        $("#current-time-label").css("left", popupLeft);
        $("#current-time-label:after").css("margin-left", popupLeft);
        $("#current-time-label > p").text(popupTime);
        
        //Delays adding the show class until the position has been adjusted.
        if(!$("#current-time-label").hasClass("show"))
            $("#current-time-label").addClass("show");
        
        updateCaption();
    }
}).mouseup(function(event) {
    if($(".caption-box.dragging, .caption-box .right-handle.dragging, .caption-box .left-handle.dragging").length > 0) {
        $("#video iframe").css("pointer-events", "");
        $("#current-time-label").removeClass("show");
        
        var right = $(".caption-box .right-handle.dragging").length > 0;
        var left = $(".caption-box .left-handle.dragging").length > 0;
        
        
        mouseDown = false;
        event.stopPropagation();

        if(right) {
            var handle = $(".caption-box .right-handle.dragging");
            handle.removeClass("dragging");
            
            if(isOver(event.pageX, event.pageY, handle.closest(".caption-box")) == false)
                handle.closest(".caption-box").removeClass("selected");

            var newWidth = handle.closest(".caption-box").position().left + handle.closest(".caption-box").width();

            $(".caption-list .caption[data-caption-id='" + handle.closest(".caption-box").attr("data-caption-id") + "']").find(".times input.end-time").val(secondsToTime(positionToTime(newWidth)));
        } else if(left) {
            var handle = $(".caption-box .left-handle.dragging");
            handle.removeClass("dragging");
            
            if(isOver(event.pageX, event.pageY, handle.closest(".caption-box")) == false)
                handle.closest(".caption-box").removeClass("selected");

            $(".caption-list .caption[data-caption-id='" + handle.closest(".caption-box").attr("data-caption-id") + "']").find(".times input.start-time").val(secondsToTime(positionToTime(handle.closest(".caption-box").position().left)));
        } else {
            var box = $(".caption-box.dragging")
            box.removeClass("dragging");
            
            if(isOver(event.pageX, event.pageY, box) == false)
                box.removeClass("selected");

            var newWidth = box.position().left + box.width();

            $(".caption-list .caption[data-caption-id='" + box.attr("data-caption-id") + "']").find(".times input.start-time").val(secondsToTime(positionToTime(box.position().left)));

            $(".caption-list .caption[data-caption-id='" + box.attr("data-caption-id") + "']").find(".times input.end-time").val(secondsToTime(positionToTime(newWidth)));
        }
        
        $(".caption-box.selected").removeClass("selected");
    }
});




var playheadUpdatingInterval;
function onPlayerStateChanged() {
    //Get the current player state.
    var currState = player.getPlayerState();
    
    //If playing, update the playhead position accordingly.
    if (currState == YT.PlayerState.PLAYING) {
        playheadUpdatingInterval = setInterval(function() {
            updatePlayhead(player.getCurrentTime());
        }, 16);
    }
    //If paused or stopped, stop movement of the timeline.
    else if(currState == YT.PlayerState.PAUSED || currState == YT.PlayerState.ENDED) {
        clearInterval(playheadUpdatingInterval);
        updatePlayhead(player.getCurrentTime());
    }
}

//Utils
$(window).resize(function() {
    if(player == null)
        return;
    
    setZoomLevel(currZoom);
    setCaption($("textarea.add-caption").val(), true);
});
function setZoomLevel(zoomLevel) {
    currZoom = zoomLevel;
    var numOfTicks = player.getDuration()/zooms[currZoom];
    tickSep = $(".waveform").width()/ticks[currZoom];
    
    $(".waveform .caption-boxes").css("width", numOfTicks * tickSep);
    $(".waveform canvas").attr("width", $(".waveform").width());
    $(".waveform canvas").css("width", $(".waveform").width());
    
    $(".waveform .caption-box").each(function() {
        updateCaptionBox($(this));
    });
    
    updatePlayhead(player.getCurrentTime());
}

//Creates new boxes when they become visible and deletes old boxes as they become invisible.
function updateVisibleBoxes() {
    var startPos = -$(".waveform .caption-boxes").position().left;
    var endPos = -$(".waveform .caption-boxes").position().left + $(".waveform").width();
    var startTime = positionToTime(startPos);
    var endTime = positionToTime(endPos);
    
    //Removes unnecessary caption boxes.
    $(".caption-boxes .caption-box").each(function() {
        var capStartPos = $(this).position().left;
        var capEndPos = $(this).position().left + $(this).width();
        var capDuration = capEndPos - capStartPos;
        
        if(capStartPos <= startPos - capDuration || capEndPos >= endPos + capDuration)
            $(this).remove();
    });
    
    $(".caption-list .caption").each(function() {
        var capStartTime = timeToSeconds($(this).find(".times input.start-time").val());
        var capEndTime = timeToSeconds($(this).find(".times input.end-time").val());
        var capDuration = capEndTime - capStartTime;
        
        if(capStartTime >= startTime - capDuration && capEndTime <= endTime + capDuration) {
            if($(".caption-boxes .caption-box[data-caption-id='" + $(this).attr("data-caption-id") + "']").length <= 0) {
                createCaptionBox($(this));
            }
        }
    });
}

function updateCaptionBox(captionBox) {
    var cID = captionBox.attr("data-caption-id");
    
    var caption = $(".caption-list .caption[data-caption-id='" + cID + "']");
    var captionText = caption.find("textarea.caption-text").val();
    var startPos = timeToPosition(timeToSeconds(caption.find(".times input.start-time").val()));
    var endPos = timeToPosition(timeToSeconds(caption.find(".times input.end-time").val()));
    
    captionBox.css("left", startPos);
    captionBox.width(endPos - startPos);
    captionBox.find("p.caption-text").text(captionText);
}

function playheadUpdated(finishedSeeking) {    
    var time = (currTime + ($(".waveform .playhead").position().left/tickSep)) * zooms[currZoom];
        
    player.seekTo(time, true);
    player.pauseVideo();
    
    updateVisibleBoxes();
    updateCaption();
}

function updatePlayhead(time, caption = undefined) {     
    //If the time left is less than the amount of time shown in a single timeline section, the playhead's position needs to be updated.
    var tmpLeft = $(".waveform .caption-boxes").position().left;
    
    //If the current time is within one width of the waveform, use the code below.
    //(For instance, if a video is 10 seconds long and the waveform shows 4 seconds at any one time, this code will be used anywhere between times 6 seconds exclusive and 10 seconds inclusive)
    if((player.getDuration() - time)/zooms[currZoom] < ticks[currZoom]) {
        //Set the timeline as far over as possible.
        $(".waveform .caption-boxes").css("left", -$(".waveform .caption-boxes").width() + $(".waveform").width());
        
        var leftTime = ($(".waveform .caption-boxes").width() - $(".waveform").width())/tickSep;
        
        if(leftTime < 0)
            leftTime = 0;
        
        currTime = Math.round(leftTime);
        
        //The number of ticks that have already passed.
        var ticksPassed = ticks[currZoom] - ((player.getDuration() - time)/zooms[currZoom]);
        
        if($(".waveform .caption-boxes").width() - $(".waveform").width() < 0)
            ticksPassed = time/zooms[currZoom];
    
        if($(".waveform .caption-boxes").position().left > 0) {
            $(".waveform .caption-boxes").css("left", 0);
        }
        
        //Set the playhead to the correct position.
        $(".waveform .playhead").css("left", ticksPassed * tickSep);
    } else {
        //This equates to: (playhead position) - ((number of ticks) * (separation per tick)).
        //It represents the left position of the playhead relative to the waveform view.
        //This is the distance between the playhead and the time
        var leftTime = ((time/zooms[currZoom]) * -tickSep) + $(".waveform .playhead").position().left;
        var tempCurrTime = (time/zooms[currZoom]) - ($(".waveform .playhead").position().left/tickSep);
                
        if(caption !== undefined) {
            leftTime = -timeToPosition(timeToSeconds(caption.find("input.start-time").val()));
            tempCurrTime = timeToSeconds(caption.find("input.start-time").val());
            
            $(".waveform .playhead").css("left", "0px");
        }
        
        leftTime = leftTime > 0 ? 0 : leftTime;
        tempCurrTime = leftTime == 0 ? 0 : tempCurrTime;
        
        $(".waveform .caption-boxes").css("left", leftTime);
        currTime = Math.round(tempCurrTime);
    }
    
    drawTicks($(".waveform canvas")[0], currZoom);
    
    updateVisibleBoxes();
    updateCaption();
    updateScrollbar($(".caption-boxes").position().left);
}

function updateScrollbar(position) {
    //Get the ratio between the timeline and its container & the scrollbar and its container.
    var ratio = -position/($(".waveform .caption-boxes").width() - $(".waveform").width());

    //Set the scrollnar's left position based on that ratio
    $(".waveform .scroll .scrollbar").css("left", ratio * ($(".waveform .scroll").width() - SCROLL_BAR_WIDTH))
}

function updateCaption() {
    var playheadPos = $(".waveform .playhead").position().left;
    $(".caption-list .caption.playing").removeClass("playing");
    
    var result = "";    
    var offset = $(".waveform .caption-boxes").position().left;
    $(".waveform .caption-box").each(function() {    
        if(playheadPos >= $(this).position().left + offset && playheadPos <= $(this).position().left + offset + $(this).width()) {
            result = $(this).find("p.caption-text").text();
            $(".caption-list .caption[data-caption-id='" + $(this).attr("data-caption-id") + "']").addClass("playing");
        }
    });
    
    setCaption(result, false);
}

function drawTicks(canvasEl, zoomLevel) {    
    //Clear the canvas
    canvas = canvasEl.getContext("2d");
    canvas.clearRect(0, 0, canvasEl.width, canvasEl.height);
    
    //Set drawing styles
    canvas.fillStyle = "#b5b5b5";
    canvas.font = "10px Arial";
    
    //Go between the current time and the number of ticks that should be drawn, with 2 extra ticks of padding
    var offset = ((timeToPosition(player.getCurrentTime())/tickSep) - currTime) * -tickSep;
    offset = offset % tickSep;
    
    for(var i = -marks[zoomLevel]; i < ticks[zoomLevel] + marks[zoomLevel]; i++) {  
        //Draw text if needed
        if((i + currTime) % marks[zoomLevel] == 0)
            canvas.fillText(secondsToTime(zooms[zoomLevel] * (i + currTime)), (tickSep * i) + offset, 25);
        
        if(i < 0)
            continue;
        
        //Draw the tick mark
        canvas.fillRect((tickSep * i) + offset, 0, 1, (i + currTime) % marks[zoomLevel] == 0 ? 10 : 5);
    }
}

//Converts a time in seconds to a position on the timeline.
function timeToPosition(seconds) {
    return (seconds/zooms[currZoom]) * tickSep;
}

//Converts a position on the timeline to the time in seconds.
function positionToTime(position) {
    return Math.round((clamp((position/tickSep) * zooms[currZoom], 0, player.getDuration())) * 10)/10;
}

//Converts a position on the timeline to the time in seconds, without clamping the decimal position.
function positionToTimeRaw(position) {
    return (clamp((position/tickSep) * zooms[currZoom], 0, player.getDuration()));
}

//Converts seconds to time text, removing hours if unneeded
function secondsToTime(seconds) {
    var hours = Math.floor(seconds/3600);
    seconds -= hours * 3600;
    var minutes = Math.floor(seconds/60);
    seconds -= minutes * 60;
    
    var result = "";
    
    
    if(hours != 0) {
        if(hours < 10)
            result += "0" + hours.toString() + ":";
        else
            result += hours.toString() + ":";
    }
    
    if(minutes < 10)
        result += "0" + minutes.toString();
    else
        result += minutes.toString();
    result += ":";
    
    if(seconds < 10)
        result += "0" + seconds.toString();
    else
        result += seconds.toString();
    
    if(Number.isInteger(seconds))
        result += ".0";
    
    return result;
}

function timeToSeconds(time) {
    var split = time.split(':');
    var total = 0;
    for(var i = split.length - 1; i >= 0; i--) {
        total += parseFloat(split[i]) * Math.pow(60, split.length - 1 - i);
    }
    
    return total;
}

//Returns an array. The first element is the boundary formed by another caption box on the right side of the provided caption box, while the second element is the ID of said box.
function captionBoxToRight(box) {    
    var nextBox = $(".caption-list .caption[data-caption-id='" + box.attr("data-caption-id") + "']").next(".caption");
    var nextID = nextBox.length > 0 ? nextBox.attr("data-caption-id") : -1;
    
    return [nextBox, nextID];
}

//Returns an array. The first element is the boundary formed by another caption box on the left side of the provided caption box, while the second element is the ID of said box.
function captionBoxToLeft(box) {    
    var prevBox = $(".caption-list .caption[data-caption-id='" + box.attr("data-caption-id") + "']").prev(".caption");
    var prevID = prevBox.length > 0 ? prevBox.attr("data-caption-id") : -1;
    
    return [prevBox, prevID];
}

//Get the minimum left position of a caption box.
function getMinLeft(box) {
    var id = box.attr("data-caption-id-prev");
    if(id == -1)
        return 0;
    
    var el = $(".caption-box[data-caption-id='" + id + "']");
    
    if(el.length <= 0)
        return 0;
    
    return el.position().left + el.width();
}

//Get the maximum right position of a caption box.
function getMaxLeft(box) {
    var id = box.attr("data-caption-id-next");
    if(id == -1)
        return timeToPosition(player.getDuration()) - box.width();
    
    var el = $(".caption-box[data-caption-id='" + id + "']");
    
    if(el.length <= 0)
        return timeToPosition(player.getDuration()) - box.width();
    
    return el.position().left;
}

//Get the maximum width of a caption box.
function getMaxRight(box) {
    var id = box.attr("data-caption-id-next");
    if(id == -1)
        return timeToPosition(player.getDuration());
    
    var el = $(".caption-box[data-caption-id='" + id + "']");
    return el.position().left;
}

//Check if the supplied x and y position is over the provided element
function isOver(x, y, el) {
     return 
        x >= el.offset().left && 
        x <= el.offset().left + el.width() && 
        y >= el.offset().top && 
        y <= el.offset().top + el.height();
}






//Studio UI
$(".waveform .caption-boxes, .waveform").mousedown(function(event) {
    $(this).addClass("moving");
}).mouseup(function() {
    $(this).removeClass("moving");
}).mousemove(function(event) {
    if($(this).hasClass("moving")) {
        if($(this).css("left") > 0) {
            $(this).css("left", 0);
        }
    }
});

$(document).mouseup(function() {
    playheadUpdated(true);
});

$("#video > .options input[type=range]").on("input", function() {
    setZoomLevel(zooms.length - 1 - $(this).val());
})

$("#options").height($("#video").height());
$(window).resize(function() {
    $("#options").height($("#video").height());
});




//Shortcuts
//Shortcut functions
function seekBackwards() {
    if(player.getCurrentTime() > 1) {
        player.seekTo(player.getCurrentTime() - 1);
    }
}

function seekForwards() {
    if(player.getCurrentTime() < player.getDuration() - 1)
        player.seekTo(player.getCurrentTime() + 1);
}

function togglePlay() {
    if(player.getPlayerState() == YT.PlayerState.PLAYING)
        player.pauseVideo();
    else
        player.playVideo();
}

function nextSub() {
    if($(".caption.selected").length > 0) {
        var next = $(".caption.selected").next(".caption");

        if(next.length > 0) {
            if($(".caption textarea.caption-text:focus").length > 0) {
                $(".caption textarea.caption-text:focus").blur()
                next.find("textarea.caption-text").focus();
            }
            if($(".caption .times input.start-time:focus").length > 0) {
                $(".caption .times input.start-time:focus").blur()
                next.find(".times input.start-time:focus").focus();
            }
            if($(".caption .times input.end-time:focus").length > 0) {
                $(".caption .times input.end-time:focus").blur()
                next.find(".times input.end-time:focus").focus();
            }
            
            $(".caption.selected").removeClass("selected");
            next.addClass("selected");
        }
    }            
}

function prevSub() {
    if($(".caption.selected").length > 0) {
        var prev = $(".caption.selected").prev(".caption");

        if(prev.length > 0) {
            if($(".caption textarea.caption-text:focus").length > 0) {
                $(".caption textarea.caption-text:focus").blur()
                prev.find("textarea.caption-text").focus();
            }
            if($(".caption .times input.start-time:focus").length > 0) {
                $(".caption .times input.start-time:focus").blur()
                prev.find(".times input.start-time:focus").focus();
            }
            if($(".caption .times input.end-time:focus").length > 0) {
                $(".caption .times input.end-time:focus").blur()
                prev.find(".times input.end-time:focus").focus();
            }
            
            $(".caption.selected").removeClass("selected");
            prev.addClass("selected");
        }
    }  
}

function insertNewline() {
    if($("input[type=text]:focus, textarea:focus").length > 0) {
        insertAtCursor($("input[type=text]:focus, textarea:focus")[0], "\n");
    }
}

function addSub() {
    if($("textarea.add-caption:focus").length > 0) {
        $("button.add-caption").click();
    } else if($(".caption textarea.caption-text:focus").length > 0) {
        $(this).blur();
    }
}

var keysDown = [];
const SHORTCUTS = {
    //SHIFT + LEFT = Seek back 1 second
    "[16, 37]": seekBackwards,
    //SHIFT + RIGHT = Seek forward 1 second
    "[16, 39]": seekForwards,
    //SHIFT + SPACE = Play/Pause
    "[16, 32]": togglePlay,
    //SHIFT + DOWN = Next subtitle
    "[16, 40]": nextSub,
    //SHIFT + UP = Previous Subtitle
    "[16, 38]": prevSub,
    //SHIFT + Enter = New line
    "[16, 13]": insertNewline,
    //Enter = Add the subtitle
    "[13]": addSub
};
$(document).on('keydown', function(event) {
    keysDown.push(event.keyCode);
    
    //Loop through all shortcuts and run the first one encountered.
    for(var keys in SHORTCUTS) {
        if(keyCombo(JSON.parse(keys))) {
            SHORTCUTS[keys]();
            event.preventDefault();
            break;
        }
    }
}).on('keyup', function(event) {
    keysDown.splice(keysDown.indexOf(event.keyCode))
});

$("#video a.shortcuts").click(function() {
    $("#shortcuts").css("display", "inline-block");
});
$("#shortcuts a.close").click(function() {
    $("#shortcuts").css("display", "");
})


//UTILS
//Check if a key combination is currently being used.
function keyCombo(keys) {
    for(var i = 0; i < keys.length; i++)
        if(keysDown.indexOf(keys[i]) == -1)
            return false;
    
    return true;
}

//CREDIT: https://stackoverflow.com/questions/11076975/how-to-insert-text-into-the-textarea-at-the-current-cursor-position
//Inserts a value at the cursor position in the provided field.
function insertAtCursor(myField, myValue) {
    //IE support
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
        myField.selectionStart = startPos + myValue.length;
        myField.selectionEnd = startPos + myValue.length;
    } else {
        myField.value += myValue;
    }
}



window.onbeforeunload = function() {
    //return "";
}