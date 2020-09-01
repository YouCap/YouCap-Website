//Generates the contents of an SRT file representing the current captions.
function generateSBVContents() {
    var result = "";
    
    $(".caption-list .caption").each(function() {
        var startTime = timeToSBVTime($(this).find(".times .start-time").val());
        var endTime = timeToSBVTime($(this).find(".times .end-time").val());
        
        if(startTime == "" || endTime == "") {
            alert("SRT TIME ERROR");
            return;
        }
        
        result += startTime + "," + endTime + "\n";
        result += $(this).find("textarea.caption-text").val() + "\n\n";
    });
    
    result = result.substr(0, result.length - 2);
    return result;
}
 
//Saves an SRT file version of the current captions.
function saveSBVFile() {
    var contents = generateSBVContents();
    var blob = new Blob([contents], {
        type: "text/plain;charset=utf-8;"
    });
    
    saveAs(blob, "captions.sbv");
}

//Loads a file to the current captions.
function loadFile(file) {
    var fileReader = new FileReader();
    
    fileReader.onload = function(evt) {
        var ext = file["name"].split('\.').pop().toLocaleLowerCase();
        
        var captions = parseCaptionFileContents(evt.target.result, ext);
                        
        $(".caption-list .caption").remove();
        $(".waveform .caption-box").remove();
        captions.forEach(function(element, index) {
            var newCaption = $(CAPTION_ITEM);
            createCaption(newCaption, element[2], [element[0], element[1]], function() {
                $(".caption-list").append(newCaption);
            })
        });
        
        updateVisibleBoxes();
    };
    
    fileReader.readAsText(file, "UTF-8");
}

//Get the automatically generated captions from YouTube.
function autoGen() {
    $("#options").addClass("generating");
    
    $.ajax({
        url: "http://localhost/backend/auto-generate.php?vid-id=" + vidID,
        success: function(data) {
            var captions = PARSER_YOUCAP(data);
                        
            $(".caption-list .caption").remove();
            $(".waveform .caption-box").remove();
            captions.forEach(function(element, index) {
                var newCaption = $(CAPTION_ITEM);
                createCaption(newCaption, element[2], [element[0], element[1]], function() {
                    $(".caption-list").append(newCaption);
                }, false);
            });
        
            updateVisibleBoxes();
        }
    }).done(function() {
        $("#options").removeClass("generating");
    });
}

function parseCaptionFileContents(contents, extension) {
    //CRLF -> LF
    contents = contents.replace(/\r\n/g, "\n");
    
    switch(extension) {
        case "srt":
            return PARSER_SRT(contents);
            break;
        case "sbv":
            return PARSER_SBV(contents);
            break;
        case "mpsub":
            return PARSER_MPSUB(contents);
            break;
        case "lrc":
            return PARSER_LRC(contents);
            break;
        case "vtt":
            return PARSER_VTT(contents);
            break;
    }
    
    return [];
}

var PARSER_SRT = function(contents) {
    //Regex for matching SRT entries
    var REGEX = new RegExp("(\\d+)\\n([\\d:,]+)\\s+-{2}\\>\\s+([\\d:,]+)\\n([\\s\\S]*?)(?=$|\\n{2}\\d+)", "gm");
    
    //The resulting matches
    var result = [];
        
    while((match = REGEX.exec(contents)) !== null) {        
        var append = [];
        append.push(timeFormat(match[2].replace(",", ".")));
        append.push(timeFormat(match[3].replace(",", ".")));
        append.push(match[4]);
        
        result.push(append);
    }
    
    return result;
}; //https://stackoverflow.com/questions/33145762/parse-a-srt-file-with-jquery-javascript

var PARSER_SBV = function(contents) {
    //Regex for matching SBV entries
    var REGEX = new RegExp("([\\d:.]+),([\\d:.]+)\\n([\\s\\S]*?)(?=$|\\n{2}(?:\\d{1,2}:)+)", "gm");
    
    //The resulting matches
    var result = [];
        
    while((match = REGEX.exec(contents)) !== null) {  
        var append = [];
        append.push(timeFormat(match[1].replace(",", ".")));
        append.push(timeFormat(match[2].replace(",", ".")));
        append.push(match[3]);
        
        result.push(append);
    }
    
    return result;
}; //https://fileinfo.com/img/ss/lg/sbv_4417.png
var PARSER_MPSUB = function(contents) {
    //Regex for matching MPSUB entries
    var REGEX = new RegExp("(\\d+(?:\\.\\d+)?) (\\d+(?:\\.\\d+)?)\\n([\\s\\S]*?)(?=\\Z|\\n{2}(?:\\d+ \\d+)+)", "gm");
    
    //The resulting matches
    var result = [];
        
    var currTime = 0.0;
    while((match = REGEX.exec(contents)) !== null) {  
        var append = [];
        
        var delay = parseFloat(match[1]);
        var length = parseFloat(match[2]);
        
        //Add the times
        currTime += delay;       
        append.push(secondsToTime(currTime));
        currTime += length;
        append.push(secondsToTime(currTime));
        
        //Add the text
        append.push(match[3]);
        
        result.push(append);
    }
    
    return result;
}; //http://www.mplayerhq.hu/DOCS/tech/mpsub.sub
var PARSER_LRC = function(contents) {
    var REGEX = new RegExp("\\[([\\d.:]+)\\](.*)", "gm");
    
    //The resulting matches
    var result = [];
    
    //The entries from the last line. Necessary because LRC files only contain times for the start of the line.
    var lastTime = "";
    var lastText = "";
    while((match = REGEX.exec(contents)) !== null) {
        if(lastTime != "") {
            var append = [];
            append.push(timeFormat(lastTime));
            append.push(timeFormat(match[1]));
            append.push(lastText);
            
            result.push(append);
        }
        
        lastTime = match[1];
        lastText = match[2].replace(/<(?:[\d:.]+)>\s?/gm, "").trim();
    }
    
    var append = [];
    append.push(timeFormat(lastTime));
    var plus05 = secondsToTime(timeToSeconds(lastTime) + 0.5)
    append.push(timeFormat(plus05));
    append.push(lastText);
    result.push(append);
    
    return result;
    
}; //https://en.wikipedia.org/wiki/LRC_(file_format)
var PARSER_VTT = function(contents) {
    var REGEX = new RegExp("(?:(\\d+)\\n)?([\\d:.]+)\\s-->\\s([\\d:.]+).*\\n([\\s\\S]*?)(?=NOTE|$|(?:\\n{2}(?:\\d+)?(?:[\\d:.]+)))", "gm");
    
    //The resulting matches
    var result = [];
        
    while((match = REGEX.exec(contents)) !== null) {        
        var append = [];
        append.push(timeFormat(match[2]));
        append.push(timeFormat(match[3]));
        append.push(match[4]);
        
        result.push(append);
    }
    
    return result;
}; //https://github.com/mozilla/vtt.js?files=1
var PARSER_YOUCAP = function(contents) {
    var REGEX = new RegExp("(\\d+\\.\\d+),(\\d+\\.\\d+)\\n([\\s\\S]*?)(?=$|\\n{2}(?:(?:\\d+\\.\\d+)))", "gm");
    
    var result = [];
    
    //The entries from the last line. Necessary because YouTube's auto-generated subtitles are in a multi-line, rolling subtitle format.
    var lastTime = "";
    var lastDuration = "";
    var lastText = "";
    while((match = REGEX.exec(contents)) !== null) {
        if(lastTime != "") {
            var append = [];
            append.push(timeFormat(secondsToTime(lastTime)));
            append.push(timeFormat(secondsToTime(match[1])));
            append.push(lastText);
            
            result.push(append);
        }
        
        lastTime = match[1];
        lastDuration = match[2];
        lastText = match[3];
    }
    
    var append = [];
    append.push(timeFormat(secondsToTime(lastTime)));
    var endTime = secondsToTime(parseFloat(lastTime) + parseFloat(lastDuration));
    append.push(timeFormat(endTime));
    append.push(lastText);
    result.push(append);
    
    return result;
}; //Created to parse contents sent from server (which in turn was obtained from YouTube)


//jQuery bindings
$("#actions > div div[name=auto-gen]").click(autoGen);
$("#actions > div div[name=upload]").click(function() {
    $("#overlay, #overlay .popup.load-file").addClass("show");
});
$("#actions > div div[name=download]").click(saveSBVFile);
$("#submit-button").click(function() {
    $("#overlay, #overlay .popup.submission").addClass("show");
});

$("#overlay .popup .buttons button.cancel").click(function() {
    $(this).closest(".popup").removeClass("show");
    $("#overlay").removeClass("show");
});
$("#overlay .popup.load-file .buttons button.submit").click(function() {
    $("#overlay .popup.load-file p.warning.temporary").remove();
    
    if($("#overlay .popup.load-file input").val() != "") {
        loadFile($("#overlay .popup.load-file input")[0].files[0]);
        $("#overlay, #overlay .popup.load-file").removeClass("show");
    }
    else
        $("<p class='temporary warning'>Please select a file.</p>").insertAfter($("#overlay .popup.load-file input"));
});
$("#overlay .popup.submission .buttons button.submit").click(function() {
    $(".popup.submission p.warning.temporary").remove();
    
    if($(".caption-list .caption").length <= 0)
        $("<p class='temporary warning'>Please add captions before submitting.</p>").insertBefore($(this).closest(".buttons"));
    else {
        var form = $(this).closest(".buttons").siblings("form");
        
        form.find("input[name=fileName]").val(vidID);
        form.find("input[name=content]").val(generateSBVContents());
        form.find("input[name=user]").val("James");
        
        $.ajax({
            url: '/backend/create-file.php',
            type: 'post',
            data: form.serialize(),
            success: function(){
                window.location = "/pages/thanks.php";
            }
        });
    }
});

//Utils
//Converts a time format ((HH:)?MM:SS.m) to SRT Format (HH:MM:SS,mmm)
function timeToSBVTime(time) {
    //Ensure that the time is correct
    time = timeFormat(time);
    
    if(time == "") {
        console.log("SRT time conversion error")
        return "";
    }
    
    if(time.split(':').length == 1)
        time = "0:" + time;
    
    time += "00";
    
    return time;
}