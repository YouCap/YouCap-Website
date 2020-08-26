$("form button.select").click(function() {
    $(this).addClass("opened");
}).blur(function() {
    $(this).removeClass("opened");
});

$("form button.select > div div").click(function(event) {
    event.stopPropagation();
    
    $(this).closest(".select").blur().find("> p").text($(this).text());
    $("form input[name=" + $(this).parent().parent().attr("name") + "]").val($(this).attr("value"));
    $("form input[name=" + $(this).parent().parent().attr("name") + "-name]").val($(this).text());
    
    $(this).closest("button.select").next("p.error-message").remove();
});

const CAPTION_ITEM = '<div class="caption"><div class="times"><input type="text" class="start-time" placeholder="00:00.0"><input type="text" class="end-time" placeholder="00:00.5"></div><textarea placeholder="Enter subtitle" class="caption-text"></textarea><div class="buttons"> <button class="delete" type="button"><img src="/images/page-icons/close.png" width=20></button> <button class="insert" type="button"><img src="/images/page-icons/add.png" width=11></button></div></div>';
const CAPTION_BOX_ITEM = '<div class="caption-box" data-caption-id="0"><span class="left-handle"></span><p class="caption-text"></p><span class="blocker"></span><span class="right-handle"></span></div>';

$(document).on({
    mouseenter: function() {
        $(this).closest(".caption").addClass("border-hover");
    },
    mouseleave: function() {
        $(this).closest(".caption").removeClass("border-hover");
    }, 
    click: function() {
        var newCaption = $(CAPTION_ITEM);
        newCaption.insertAfter($(this).closest(".caption"));
        
        //Gets times
        var times = setTimes(newCaption);
        
        //If error was thrown
        if(times[0] == -1 && times[1] == -1) {
            var button = $(this);
            var caption = $(this).closest(".caption");
            button.addClass("error");
            caption.addClass("error");
            setTimeout(function() {
                button.removeClass("error")
                caption.removeClass("error");
            }, 200);
        } else {        
            var newCaptionBox = $(CAPTION_BOX_ITEM);
            newCaptionBox.attr("data-caption-id", CAPTION_ID);
            newCaption.attr("data-caption-id", CAPTION_ID);
            CAPTION_ID++;

            newCaptionBox.find("p.caption-text").text($("textarea.add-caption").val());
            $("textarea.add-caption").val("");

            newCaptionBox.insertBefore($(".waveform .playhead"));
            updateCaptionBox(newCaptionBox);
            
            newCaptionBox.attr("data-caption-id-prev", captionBoxToLeft(newCaptionBox)[1]);
            newCaptionBox.attr("data-caption-id-next", captionBoxToRight(newCaptionBox)[1]);
                                    
            $(".waveform .caption-box[data-caption-id='" + newCaptionBox.attr("data-caption-id-prev") + "']").attr("data-caption-id-next", newCaptionBox.attr("data-caption-id"));
            $(".waveform .caption-box[data-caption-id='" + newCaptionBox.attr("data-caption-id-next") + "']").attr("data-caption-id-prev", newCaptionBox.attr("data-caption-id"));
        }
    }
}, ".caption .buttons button.insert");

$(document).on({
    mouseenter: function() {
        $(this).find("img").attr("src", "/images/page-icons/close-hover.png");
    },
    mouseleave: function() {
        $(this).find("img").attr("src", "/images/page-icons/close.png");
    }, 
    click: function() {
        $(".caption-box[data-caption-id='" + $(".caption-box[data-caption-id='" + $(this).closest(".caption").attr("data-caption-id") + "']").attr("data-caption-id-prev") + "']").attr("data-caption-id-next", $(".caption-box[data-caption-id='" + $(this).closest(".caption").attr("data-caption-id") + "']").attr("data-caption-id-next"));
        
        $(".caption-box[data-caption-id='" + $(".caption-box[data-caption-id='" + $(this).closest(".caption").attr("data-caption-id") + "']").attr("data-caption-id-next") + "']").attr("data-caption-id-prev", $(".caption-box[data-caption-id='" + $(this).closest(".caption").attr("data-caption-id") + "']").attr("data-caption-id-prev"));
        
        $(".caption-box[data-caption-id='" + $(this).closest(".caption").attr("data-caption-id") + "']").remove();
        $(this).closest(".caption").remove(); 
    }
}, ".caption .buttons button.delete")
.on({
    focus: function() {
        $(".caption.selected").removeClass("selected");
        $(this).closest(".caption").addClass("selected");
    }
}, ".caption textarea, .caption .times input")
.on({
    keyup: function() {
        var cID = $(this).parent().attr("data-caption-id");
        $(".waveform .caption-box[data-caption-id='" + cID + "'] .caption-text").text($(this).val());
        console.log(cID);
    }
}, ".caption textarea")
.on({
    keyup: function() {
        var cID = $(this).closest(".caption").attr("data-caption-id");
        
        var startPos = timeToPosition(timeToSeconds($(this).parent().find(".start-time").val()));
        var endPos = timeToPosition(timeToSeconds($(this).parent().find(".end-time").val()));

        console.log(endPos - startPos);
        
        $(".waveform .caption-box[data-caption-id='" + cID + "']").css("left", startPos);
        $(".waveform .caption-box[data-caption-id='" + cID + "']").width(endPos - startPos);
    }
}, ".caption .times input");






//Captioning
var CAPTION_ID = 0;
$("button.add-caption").click(function() {
    var newCaption = $(CAPTION_ITEM);
    newCaption.find(".caption-text").text($("textarea.add-caption").val());

    console.log($(".caption.selected").length)
    if($(".caption.selected").length <= 0)
        newCaption.addClass("selected").prependTo($(".caption-list"));
    else
        newCaption.insertBefore($(".caption.selected"));
    
    var times = setTimes(newCaption);

    //If error was thrown
    if(times[0] == -1 && times[1] == -1) {
        var button = $(this);
        button.addClass("error");
        setTimeout(function() {
            button.removeClass("error")
        }, 200);
    } else {        
        var newCaptionBox = $(CAPTION_BOX_ITEM);
        newCaptionBox.attr("data-caption-id", CAPTION_ID);
        newCaption.attr("data-caption-id", CAPTION_ID);
        CAPTION_ID++;
        
        newCaptionBox.find("p.caption-text").text($("textarea.add-caption").val());
        $("textarea.add-caption").val("");
        
        newCaptionBox.insertBefore($(".waveform .playhead"));
        updateCaptionBox(newCaptionBox);
            
        newCaptionBox.attr("data-caption-id-prev", captionBoxToLeft(newCaptionBox)[1]);
        newCaptionBox.attr("data-caption-id-next", captionBoxToRight(newCaptionBox)[1]);
                        
        $(".waveform .caption-box[data-caption-id='" + newCaptionBox.attr("data-caption-id-prev") + "']").attr("data-caption-id-next", newCaptionBox.attr("data-caption-id"));
        $(".waveform .caption-box[data-caption-id='" + newCaptionBox.attr("data-caption-id-next") + "']").attr("data-caption-id-prev", newCaptionBox.attr("data-caption-id"));
    }
});

$(document).on({
    change: function() {    
        var prev = $(this).closest(".caption").prev(".caption");
        if(prev.length != 0)
            if(timeToSeconds(prev.find(".times .end-time").val()) > timeToSeconds($(this).val()))
                $(this).val(prev.find(".times .end-time").val());
        
        if($(this).val().match(".*[^0-9:.].*")) {
            if($(this).attr("data-old") == undefined)
                $(this).val("");
            else
                $(this).val($(this).attr("data-old"));
        } else {
            $(this).val(timeFormat($(this).val()));
            $(this).attr("data-old", $(this).val());
        }
    }
}, ".caption .times input.start-time")
.on({
    change: function() {    
        var next = $(this).closest(".caption").next(".caption");
        if(next.length != 0)
            if(timeToSeconds(next.find(".times .start-time").val()) < timeToSeconds($(this).val()))
                $(this).val(next.find(".times .start-time").val());
        
        if($(this).val().match(".*[^0-9:.].*")) {
            if($(this).attr("data-old") == undefined)
                $(this).val("");
            else
                $(this).val($(this).attr("data-old"));
        } else {
            $(this).val(timeFormat($(this).val()));
            $(this).attr("data-old", $(this).val());
        }
    }
}, ".caption .times input.end-time");


function createCaptionSlider(caption) {
    
}