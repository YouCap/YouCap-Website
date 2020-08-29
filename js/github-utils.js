//Returns a string of 'none', 'review', or 'published', representing that no file exists, one is in review, or one has been pubished respectively.
function fileExists(vidID, language) {
    var result = "";
    
    $.ajax({
        url: "https://raw.githubusercontent.com/YouCap/captions-" + language.toLocaleLowerCase() + "-0/master/" + vidID,
        success: function(data, textStatus, xhr) {
            if(xhr.status == 200)
                result = 'published';
        },
        async: false
    });
    
    if(result != "")
        return result;
    
    return fileInReview(vidID, language) ? 'review' : 'none';
}

//Returns a boolean representing whether a subtitle file is in review for the provided video and language.
function fileInReview(vidID, language) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
       if(this.status == 200)
           return true;
    };
        
    xhttp.open("GET", "https://raw.githubusercontent.com/YouCap/captions-" + language.toLocaleLowerCase() + "-review-0/master/" + vidID, false);
    xhttp.send();
    
    return false;
}