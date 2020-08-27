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
        result += ">> " + $(this).find("textarea.caption-text").val() + "\n\n";
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

//Loads an SRT file to the current captions.
function loadSRTFile(contents) {
    
}

var PARSER_SRT = function() {}; //https://stackoverflow.com/questions/33145762/parse-a-srt-file-with-jquery-javascript
var PARSER_SBV = function() {}; //https://fileinfo.com/img/ss/lg/sbv_4417.png
var PARSER_MPSUB = function() {}; //http://www.mplayerhq.hu/DOCS/tech/mpsub.sub
var PARSER_LRC = function() {}; //https://en.wikipedia.org/wiki/LRC_(file_format)
var PARSER_CAP = function() {}; //https://drive.google.com/file/d/0B9DJydDVOVKKLTJpU3Qtei02NGxzZ0c0QUp6VXAwNUYzTnV3/view
var PARSER_VTT = function() {}; //https://github.com/mozilla/vtt.js?files=1


//jQuery bindings
$("#actions > div div[name=upload]").click(function() {
    $("#overlay, #overlay .popup.load-file").addClass("show");
});
$("#actions > div div[name=download]").click(saveSBVFile);

$("#overlay .popup.load-file .buttons button.cancel").click(function() {
    $("#overlay, #overlay .popup.load-file").removeClass("show");
});
$("#overlay .popup.load-file .buttons button.submit").click(function() {
    $("#overlay .popup.load-file p.warning.temporary").remove();
    
    if($("#overlay .popup.load-file input").val() != "") {
        loadSRTFile();
        $("#overlay, #overlay .popup.load-file").removeClass("show");
    }
    else
        $("<p class='warning temporary'>Please select a file.</p>").insertAfter($("#overlay .popup.load-file input"));
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