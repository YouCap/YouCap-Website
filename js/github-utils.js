//Returns a string of 'none', 'review', or 'published', representing that no file exists, one is in review, or one has been pubished respectively.
function fileExists(vidID, language, onResult) {    
    $.ajax({
        url: "https://raw.githubusercontent.com/YouCap/captions-" + language.toLocaleLowerCase() + "-0/main/published/" + vidID,
        success: function(data, textStatus, xhr) {
            if(xhr.status == 200)
                onResult('published');
        },
        statusCode: {
            404: function() {
                fileInReview(vidID, language, onResult);
            }
        }
    });
}

//Returns a boolean representing whether a subtitle file is in review for the provided video and language.
function fileInReview(vidID, language, onResult) {    
    $.ajax({
        url: "https://raw.githubusercontent.com/YouCap/captions-" + language.toLocaleLowerCase() + "-0/main/review/" + vidID,
        success: function(data, textStatus, xhr) {
            if(xhr.status == 200)
                onResult('review');
        },
        statusCode: {
            404: function() {
                onResult('none');
            }
        }
    })
}