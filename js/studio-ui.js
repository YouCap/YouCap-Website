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

//For creating new caption and caption box items
const CAPTION_ITEM = '<div class="caption"><div class="times"><input type="text" class="start-time" placeholder="00:00.0"><input type="text" class="end-time" placeholder="00:00.5"></div><textarea placeholder="Enter subtitle" class="caption-text"></textarea><div class="buttons"> <button class="delete" type="button"><img src="/images/page-icons/close.png" width=20></button> <button class="insert" type="button"><img src="/images/page-icons/add.png" width=11></button></div><div class="buttons arrow"><button type="button" class="arrow up"><img src="/images/page-icons/arrow.png" width=16></button><button type="button" class="arrow down"><img src="/images/page-icons/arrow.png" width=16></button></div></div>';
const CAPTION_BOX_ITEM = '<div class="caption-box" data-caption-id="0"><span class="left-handle"></span><p class="caption-text"></p><span class="blocker"></span><span class="right-handle"></span></div>';

$(document).on({
    //When a caption is clicked, it should be selected
    click: function() {
        $(".caption.selected").removeClass("selected");
        $(this).addClass("selected");
        
        var time = timeToSeconds($(this).find(".times input.start-time").val());
        player.seekTo(time, true);
        updatePlayhead(time);
    }
}, ".caption")
.on({
    //When a caption's insert button is hovered over, add a small colorfull border to the bottom of the caption
    mouseenter: function() {
        $(this).closest(".caption").addClass("border-hover");
    },
    mouseleave: function() {
        $(this).closest(".caption").removeClass("border-hover");
    }, 
    //When the insert button is pressed, insert a new caption immediately after this caption
    click: function() {
        //Create and insert
        var newCaption = $(CAPTION_ITEM);
        var currCaption = $(this).closest(".caption");
        
        createCaption(newCaption, "", [], function(caption) {
            newCaption.insertAfter(currCaption);
                
            setTimeout(function() {
                $(".caption[data-caption-id='" + newCaption.attr("data-caption-id") + "']").click();
                $(".caption[data-caption-id='" + newCaption.attr("data-caption-id") + "'] textarea.caption-text").focus();
                
                if($("#video .options .checkbox input[type=checkbox]").is(":checked"))
                    player.pauseVideo();
            }, 50);
        });
        
//        
//        //If there's not enough space for the new caption
//        if(times[0] == -1 && times[1] == -1) {
//            //Delete everything and flash a small error symbol.
//            newCaption.remove();
//            
//            var button = $(this);
//            var caption = $(this).closest(".caption");
//            button.addClass("error");
//            caption.addClass("error");
//            setTimeout(function() {
//                button.removeClass("error")
//                caption.removeClass("error");
//            }, 200);
//        } else {      
//            //Create a corresponding caption box and set both elements to have the correct caption ID
//            var newCaptionBox = $(CAPTION_BOX_ITEM);
//            newCaptionBox.attr("data-caption-id", CAPTION_ID);
//            newCaption.attr("data-caption-id", CAPTION_ID);
//            CAPTION_ID++;
//
//            //Insert the caption box
//            newCaptionBox.insertBefore($(".waveform .playhead"));
//            
//            //Set the caption box's position in relation to the times on the caption
//            updateCaptionBox(newCaptionBox);
//            
//            var currID = newCaption.attr("data-caption-id");
//            var prevID = captionBoxToLeft(newCaptionBox)[1];
//            var nextID = captionBoxToRight(newCaptionBox)[1]
//            
//            newCaption.attr("data-caption-id-prev", prevID);
//            newCaption.attr("data-caption-id-next", nextID);
//            
//            newCaptionBox.attr("data-caption-id-prev", prevID);
//            newCaptionBox.attr("data-caption-id-next", nextID);
//            
//            //Set the data-caption-id-prev and data-caption-id-next attributes for the surrounding captions and caption boxes.
//            $(".waveform .caption-box[data-caption-id='" + prevID + "']").attr("data-caption-id-next", currID);
//            $(".waveform .caption-box[data-caption-id='" + nextID + "']").attr("data-caption-id-prev", currID);
//            
//            $(".caption-list .caption[data-caption-id='" + prevID + "']").attr("data-caption-id-next", currID);
//            $(".caption-list .caption[data-caption-id='" + nextID + "']").attr("data-caption-id-prev", currID);
//            
//            //Update the currently displayed caption in case the insertion is now under the playhead. 
//            updateCaption();
//        }
    }
}, ".caption .buttons button.insert");

$(document).on({
    //Hovering for the close button image
    mouseenter: function() {
        $(this).find("img").attr("src", "/images/page-icons/close-hover.png");
    },
    mouseleave: function() {
        $(this).find("img").attr("src", "/images/page-icons/close.png");
    }, 
    click: function() {
        //Get some IDs
        var currID = $(this).closest(".caption").attr("data-caption-id");
        var prevID = $(".caption-box[data-caption-id='" + currID + "']").attr("data-caption-id-prev");
        var nextID = $(".caption-box[data-caption-id='" + currID + "']").attr("data-caption-id-next");
        
        //Change the surrounding captions and caption boxes to have correct previous and next IDs, taking into account the soon to be deleted caption.
        $(".caption-box[data-caption-id='" + prevID + "']").attr("data-caption-id-next", nextID);
        
        $(".caption-box[data-caption-id='" + nextID + "']").attr("data-caption-id-prev", prevID);
        
        $(".caption-list .caption[data-caption-id='" + prevID + "']").attr("data-caption-id-next", nextID);
        
        $(".caption-list .caption[data-caption-id='" + nextID + "']").attr("data-caption-id-prev", prevID);
        
        //Remove the caption/caption box.
        $(".caption-box[data-caption-id='" + $(this).closest(".caption").attr("data-caption-id") + "']").remove();
        $(this).closest(".caption").remove(); 
    }
}, ".caption .buttons button.delete")
.on({
    //Set selected on focus
    focus: function() {
        $(".caption.selected").removeClass("selected");
        $(this).closest(".caption").addClass("selected");
    }
}, ".caption textarea, .caption .times input")
.on({
    keyup: function() {
        var cID = $(this).parent().attr("data-caption-id");
        $(".waveform .caption-box[data-caption-id='" + cID + "'] .caption-text").text($(this).val());
        
        if($("#video .options .checkbox input[type=checkbox]:checked").length > 0)
            player.pauseVideo();
        
        setCaption($(this).val(), false);
    }
}, ".caption textarea")
.on({
    keyup: function() {
        var cID = $(this).closest(".caption").attr("data-caption-id");
        
        var startPos = timeToPosition(timeToSeconds($(this).parent().find(".start-time").val()));
        var endPos = timeToPosition(timeToSeconds($(this).parent().find(".end-time").val()));
        
        $(".waveform .caption-box[data-caption-id='" + cID + "']").css("left", startPos);
        $(".waveform .caption-box[data-caption-id='" + cID + "']").width(endPos - startPos);
        
        if($("#video .options .checkbox input[type=checkbox]:checked").length > 0)
            player.pauseVideo();
    }
}, ".caption .times input")
.on({
    click: function() {
        var currID = $(this).closest(".caption").attr("data-caption-id");
        var prevID = $(this).closest(".caption").attr("data-caption-id-prev");
        
        var currText = $(this).closest(".caption").find(".caption-text").val();
        var prevText = $(".caption-list .caption[data-caption-id='" + prevID + "'] .caption-text").val();
        
        $(this).closest(".caption").find(".caption-text").val(prevText);
        $(".caption-box[data-caption-id='" + currID + "']").find("p.caption-text").text(prevText);
        
        $(".caption-list .caption[data-caption-id='" + prevID + "'] .caption-text").val(currText);
        $(".caption-box[data-caption-id='" + prevID + "'] .caption-text").text(currText);
        
        $(this).closest(".caption").removeClass("selected");
        $(".caption-list .caption[data-caption-id='" + prevID + "']").addClass("selected");
    }
}, ".caption .buttons.arrow .up")
.on({
    click: function() {
        var currID = $(this).closest(".caption").attr("data-caption-id");
        var nextID = $(this).closest(".caption").attr("data-caption-id-next");
                
        var currText = $(this).closest(".caption").find(".caption-text").val();
        var nextText = $(".caption-list .caption[data-caption-id='" + nextID + "'] .caption-text").val();
        
        $(this).closest(".caption").find(".caption-text").val(nextText);
        $(".caption-box[data-caption-id='" + currID + "']").find("p.caption-text").text(nextText);
        
        $(".caption-list .caption[data-caption-id='" + nextID + "'] .caption-text").val(currText);
        $(".caption-box[data-caption-id='" + nextID + "'] .caption-text").text(currText);
        
        $(this).closest(".caption").removeClass("selected");
        $(".caption-list .caption[data-caption-id='" + nextID + "']").addClass("selected");
    }
}, ".caption .buttons.arrow .down");






//Captioning
function createCaption(newCaption, captionText, times, insertionCallback, createBox=true) {
    newCaption.find(".caption-text").html(captionText);
    
    insertionCallback(newCaption);
    
    if(times.length == 0)
        //Get the times available after this caption (max of 5 seconds long)
        times = setTimes(newCaption);
    
    newCaption.find(".times input.start-time").val(times[0]);
    newCaption.find(".times input.end-time").val(times[1]);
    
    //If error was thrown
    if(times[0] == -1 && times[1] == -1) {
        var button = $(this);
        button.addClass("error");
        setTimeout(function() {
            button.removeClass("error")
        }, 200);
    } else {        
        newCaption.attr("data-caption-id", CAPTION_ID);
        CAPTION_ID++;
        
        $("textarea.add-caption").val("");
                    
        var currID = newCaption.attr("data-caption-id");
        var prevID = captionBoxToLeft(newCaption)[1];
        var nextID = captionBoxToRight(newCaption)[1]

        newCaption.attr("data-caption-id-prev", prevID);
        newCaption.attr("data-caption-id-next", nextID);
        
        if(createBox)
            var newCaptionBox = createCaptionBox(newCaption);
        
        $(".caption-list .caption[data-caption-id='" + prevID + "']").attr("data-caption-id-next", currID);
        $(".caption-list .caption[data-caption-id='" + nextID + "']").attr("data-caption-id-prev", currID);

        updateCaption();
    }
}

function createCaptionBox(caption) {
    var currID = caption.attr("data-caption-id");
    var prevID = caption.attr("data-caption-id-prev");
    var nextID = caption.attr("data-caption-id-next");
    
    var newCaptionBox = $(CAPTION_BOX_ITEM);
    newCaptionBox.attr("data-caption-id", currID);

    newCaptionBox.find("p.caption-text").text($("textarea.add-caption").val());
    $("textarea.add-caption").val("");

    newCaptionBox.appendTo($(".waveform .caption-boxes"));
    updateCaptionBox(newCaptionBox);

    newCaptionBox.attr("data-caption-id-prev", prevID);
    newCaptionBox.attr("data-caption-id-next", nextID);

    $(".waveform .caption-box[data-caption-id='" + prevID + "']").attr("data-caption-id-next", currID);
    $(".waveform .caption-box[data-caption-id='" + nextID + "']").attr("data-caption-id-prev", currID);

    updateCaption();
    //updateVisibleBoxes()
    
    return newCaptionBox;
}

var CAPTION_ID = 0;
$("button.add-caption").click(function() {
    var newCaption = $(CAPTION_ITEM);
    var times = setTimes(newCaption);

    createCaption(newCaption, $("textarea.add-caption").val(), [], function(caption) {
        if($(".caption.selected").length <= 0)
            caption.addClass("selected").prependTo($(".caption-list"));
        else
            caption.insertBefore($(".caption.selected"));
    });
});
$("textarea.add-caption").on("keyup", function(event) { 
    if($("#video .options .checkbox input[type=checkbox]:checked").length > 0 && !keyCombo([16, 32]) && event.which != 16)
        player.pauseVideo();
    
    if(player.getPlayerState() != YT.PlayerState.PLAYING)
        setCaption($(this).val(), true);
}).on("focusin", function() {
    if(player.getPlayerState() != YT.PlayerState.PLAYING)
        setCaption($(this).val(), true);
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
        window.location = "/pages/studio.php?vid-id=" + vidID + "&vid-lang=" + langEl.attr("value") + "&vid-lang-name=" + langEl.text();
    else
        $("<p class='warning'>Please select a language.</p>").insertAfter($(this).closest(".buttons").siblings(".select.standard-ui"));
});

$("#overlay .popup .buttons button.cancel").click(function() {
    $(this).closest(".popup").removeClass("show");
    
    if($("#overlay .popup.show").length <= 0)
        $("#overlay").removeClass("show");
});