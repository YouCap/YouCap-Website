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
});

const CAPTION_ITEM = '<div class="caption"><div class="times"><input type="text" class="start-time" placeholder="00:00.0"><input type="text" class="end-time" placeholder="00:00.5"></div><textarea placeholder="Enter subtitle" class="caption-text"></textarea><div class="buttons"> <button class="delete" type="button"><img src="/images/page-icons/close.png" width=20></button> <button class="insert" type="button"><img src="/images/page-icons/add.png" width=11></button></div></div>';

$(document).on({
    mouseenter: function() {
        $(this).closest(".caption").addClass("border-hover");
    },
    mouseleave: function() {
        $(this).closest(".caption").removeClass("border-hover");
    }, 
    click: function() {
        $(CAPTION_ITEM).insertAfter($(this).closest(".caption")); 
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
        $(this).closest(".caption").remove(); 
    }
}, ".caption .buttons button.delete")
.on({
    focus: function() {
        $(".caption.selected").removeClass("selected");
        $(this).closest(".caption").addClass("selected");
    }
}, ".caption textarea, .caption .times input");






//Captioning
$("button.add-caption").click(function() {
    var newCaption = $(CAPTION_ITEM);
    newCaption.find(".caption-text").text($("textarea.add-caption").text());
    
    console.log($(".caption.selected").length)
    if($(".caption.selected").length <= 0)
        newCaption.addClass("selected").prependTo($(".caption-list"));
    else
        newCaption.insertBefore($(".caption.selected"));
});

$(document).on({
    change: function() {    
        var prev = $(this).closest(".caption").prev(".caption");
        if(prev.length != 0)
            if(timeToSeconds(prev.find(".times .end-time").val()) > timeToSeconds($(this).val()))
                $(this).val(prev.find(".times .end-time").val());
        
        if($(this).val().match(".*[^0-9:.].*")) {
            if($(this).data("old") == undefined)
                $(this).val("");
            else
                $(this).val($(this).data("old"));
        } else {
            $(this).val(timeFormat($(this).val()));
            $(this).data("old", $(this).val());
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
            if($(this).data("old") == undefined)
                $(this).val("");
            else
                $(this).val($(this).data("old"));
        } else {
            $(this).val(timeFormat($(this).val()));
            $(this).data("old", $(this).val());
        }
    }
}, ".caption .times input.end-time");



//Utils
//Formats an entered value into a string in the format of MM:SS.MS
function timeFormat(value) {    
    if(value.match("^([0-9]*:)?[0-9]{2}:[0-9]{2}\\.[0-9]$"))
        return value;
    
    var steps = ["[0-9]", "[0-9]", ":", "[0-9]", "[0-9]", "\\.", "[0-9]+", ""];
    var placeholders = "00:00.0";
    
    var match = "";
    for(var i = steps.length - 1; i >= 0; i--) {
        match = steps[i] + match;
        if(value.match("^" + match + "$"))
            return placeholders.substring(0, i) + value.substring(0, steps.length - 1 - i);
    }
    
    if(value.match("^[0-9]+$"))
        return "00:00." + value.substring(0, 1);
    
    if(value.match("^\\.[0-9]+$"))
        return "00:00" + value.substring(0, 2);
    
    if(value.match("^[0-9]\\.[0-9]+$"))
        return "00:0" + value.substr(0, 3);
}