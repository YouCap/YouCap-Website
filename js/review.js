//Load Youtube Iframe API
var tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

//Create video player
var player;
window.onYouTubeIframeAPIReady = function() {
    loadReviewCaptions();
}

//Sets the title
function onPlayerReady() {
    $(".title").text(player.getVideoData()["title"]);
}

var startTime;
var elapsedTime = 0;

function onPlayerStateChanged() {
    //Get the current player state.
    var currState = player.getPlayerState();
    
    if(currState == YT.PlayerState.PLAYING) {
        startTime = (new Date()).getTime();
        changeReviewCaption();
    } else if(currState == YT.PlayerState.PAUSED || currState == YT.PlayerState.ENDED) {
        elapsedTime += ((new Date()).getTime() - startTime);
    }
    
    if(elapsedTime/1000 >= player.getDuration())
        console.log("FULL VIDEO WATCHED");
};

function loadReviewCaptions() {
    $.ajax({
        url: "/backend/get-review-caption.php",
        success: function(data) {
            console.log(typeof(data));
            data = "jtXw0VnW9l4\n00:00:00.0,00:00:05.0\n*Intro*";

            var newLineSplit = data.indexOf('\n');
            player = new YT.Player('player', {
                height: '390',
                width: '640',
                videoId: data.substring(0, newLineSplit),
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChanged
                },
                playerVars: {
                    fs: 0
                }
            });
    
            var captions = PARSER_YOUCAP_SBV(data.substring(newLineSplit));
            console.log(captions);
            for(var i = 0; i < captions.length; i++) {
                $("<div class='review-caption' data-caption-id='" + i + "' data-seconds=" + captions[i][0] + "><p class='start-time inline'>" + secondsToTime(captions[i][0]) + "</p><p class='caption-text inline'>" + captions[i][2] + "</p></div>").appendTo($(".caption-list"));
            }
        }
    })
}

function changeReviewCaption() {
    var result;
    $(".caption-list .review-caption").each(function() {
        if($(this).data("seconds") <= player.getCurrentTime())
            result = $(this);
    })
    
    if(result) {
        $(".caption-list .review-caption.playing").removeClass("playing");
        result.addClass("playing");
        setCaption(result.find("p.caption-text").text(), false);
        
        $(".caption-list").scrollTop(result.position().top + ($(".caption-list").height()/2));
    }
}

$(document).on({
    click: function() {
        player.seekTo($(this).data("seconds"), true);
        changeReviewCaption();
    }
}, ".caption-list .review-caption");

//Parses a saved YouCap SBV caption file to an interpretable format.
var PARSER_YOUCAP_SBV = function(contents) {
    //Regex for matching SBV entries
    var REGEX = new RegExp("([\\d:.]+),([\\d:.]+)\\n([\\s\\S]*?)(?=$|\\n{2}(?:\\d{1,2}:)+)", "gm");
    
    //The resulting matches
    var result = [];
        
    while((match = REGEX.exec(contents)) !== null) {  
        var append = [];
        append.push(timeToSeconds(timeFormat(match[1].replace(",", "."))));
        append.push(timeToSeconds(timeFormat(match[2].replace(",", "."))));
        append.push(match[3]);
        
        result.push(append);
    }
    
    return result;
}; //https://fileinfo.com/img/ss/lg/sbv_4417.png





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









$("form a.switch-language").click(function() {
    $("#overlay").addClass("show");
    $("#overlay .popup.switch-language").addClass("show");
});
$("#overlay .popup.switch-language button.select > div div").click(function() {
    $(this).siblings().removeClass("selected");
    $(this).addClass("selected");
})
$("#overlay .popup.switch-language .buttons button.submit").click(function() {
    $(this).closest(".popup.switch-language").children("p.warning").remove();
    
    var langEl = $("#overlay .popup.switch-language button.select > div div.selected");
    
    if(langEl.length > 0)
        window.location = "/pages/review.php?vid-id=" + vidID + "&vid-lang=" + langEl.attr("value") + "&vid-lang-name=" + langEl.text();
    else
        $("<p class='warning'>Please select a language.</p>").insertAfter($(this).closest(".buttons").siblings(".select.standard-ui"));
});

$("button.select").click(function() {
    $(this).addClass("opened");
}).blur(function() {
    $(this).removeClass("opened");
});

$("button.select > div div").click(function(event) {
    event.stopPropagation();
    
    $(this).closest(".select").blur()
    
    $("form input[name=" + $(this).closest(".select").attr("name") + "-name]").val($(this).text());
    
    if($(this).attr("value") != "")
        $("form input[name=" + $(this).closest(".select").attr("name") + "]").val($(this).attr("value"));
    
    if(!$(this).closest(".select").hasClass("select-no-change")) {
        $(this).closest(".select").find("> p").text($(this).text());
    }
    
    $(this).closest("button.select").next("p.error-message").remove();
});

$("#overlay .popup .buttons button.cancel").click(function() {
    $(this).closest(".popup").removeClass("show");
    $("#overlay").removeClass("show");
});