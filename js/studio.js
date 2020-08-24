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
    $(".waveform canvas").draggable({
        axis: 'x',       
        drag: function(event, ui){            
            //Stops from going too far to the left (below 00:00)
            ui.position.left = Math.min(ui.position.left, 0);
            
            //Stops from going too far to the right (above the video length)
            ui.position.left = Math.max(ui.position.left, ((player.getDuration()/zooms[currZoom]) * -tickSep) + $(".waveform").width());
            
            //Adjust the scrollbar position
            
            //Get the ratio between the timeline and its container & the scrollbar and its container.
            var ratio = -ui.position.left/($(".waveform canvas").width() - $(".waveform").width());
            
            //Set the scrollnar's left position based on that ratio
            $(".waveform .scroll .scrollbar").css("left", ratio * ($(".waveform .scroll").width() - SCROLL_BAR_WIDTH))
            
            //Set the current time
            currTime = Math.ceil(-ui.position.left/tickSep);
            
            //Update the drawn tick marks
            drawTicks(this, currZoom);
            
            playheadUpdated(false);
        }
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
            $(".waveform canvas").css("left", ratio * ($(".waveform canvas").width() - $(".waveform").width()))
            
            //Set the current time based on the position of the timeline
            currTime = Math.ceil(-$(".waveform canvas").position().left/tickSep);
            
            drawTicks($(".waveform canvas")[0], currZoom);
            
            playheadUpdated(false);
        }
    });
}


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
function setZoomLevel(zoomLevel) {
    currZoom = zoomLevel;
    var numOfTicks = player.getDuration()/zooms[currZoom];
    tickSep = $(".waveform").width()/ticks[currZoom];
    
    $(".waveform canvas").attr("width", numOfTicks * tickSep);
    $(".waveform canvas").css("width", numOfTicks * tickSep);   
    
    updatePlayhead(player.getCurrentTime());
}

function playheadUpdated(finishedSeeking) {    
    var time = (currTime + ($(".waveform .playhead").position().left/tickSep)) * zooms[currZoom];
    
    player.seekTo(time, true);
    player.pauseVideo();
}

function updatePlayhead(time) {      
    //If the time left is less than the amount of time shown in a single timeline section, the playhead's position needs to be updated.
    if((player.getDuration() - time)/zooms[currZoom] < ticks[currZoom]) {
        //Set the timeline as far over as possible.
        $(".waveform canvas").css("left", -$(".waveform canvas").width() + $(".waveform").width());
        
        var leftTime = ($(".waveform canvas").width() - $(".waveform").width())/tickSep;
        
        if(leftTime < 0)
            leftTime = 0;
        
        currTime = Math.round(leftTime);
        
        //The number of ticks that have already passed.
        var ticksPassed = ticks[currZoom] - ((player.getDuration() - time)/zooms[currZoom]);
        
        if($(".waveform canvas").width() - $(".waveform").width() < 0)
            ticksPassed = time/zooms[currZoom];
    
        if($(".waveform canvas").position().left > 0) {
            $(".waveform canvas").css("left", 0);
        }
        
        //Set the playhead to the correct position.
        $(".waveform .playhead").css("left", ticksPassed * tickSep);
    } else {        
        $(".waveform canvas").css("left", ((time/zooms[currZoom]) * -tickSep) + $(".waveform .playhead").position().left);
        currTime = Math.round(time/zooms[currZoom]);
    }
    
    drawTicks($(".waveform canvas")[0], currZoom);
}

function drawTicks(canvasEl, zoomLevel) {    
    //Clear the canvas
    canvas = canvasEl.getContext("2d");
    canvas.clearRect(0, 0, canvasEl.width, canvasEl.height);
    
    //Set drawing styles
    canvas.fillStyle = "#b5b5b5";
    canvas.font = "10px Arial";
    
    //Go between the current time and the number of ticks that should be drawn, with 2 extra ticks of padding
    for(var i = currTime - marks[zoomLevel]; i < currTime + ticks[zoomLevel] + marks[zoomLevel]; i++) {  
        if(i < 0)
            continue;
        
        //Draw the tick mark
        canvas.fillRect(tickSep * i, 0, 1, i % marks[zoomLevel] == 0 ? 10 : 5);
        
        //Draw text if needed
        if(i % marks[zoomLevel] == 0)
            canvas.fillText(secondsToTime(zooms[zoomLevel] * i), tickSep * i, 25);
    }
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







//Studio UI
$(".waveform canvas, .waveform").mousedown(function(event) {
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

$(".waveform canvas, .waveform .playhead, .waveform .scroll .scrollbar").mouseup(function() {
    playheadUpdated(true);
});

$("#video > .options input[type=range]").on("input", function() {
    setZoomLevel(zooms.length - 1 - $(this).val());
})

$("#options").height($("#video").height());
$(window).resize(function() {
    $("#options").height($("#video").height());
});






window.onbeforeunload = function() {
    //return "";
}