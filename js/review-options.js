$("#options button[type=submit]").click(function(event) {
    event.preventDefault();
    
    if(!checkCreateForm())
        return;
    
    if($("#video .checkbox > input[name=nsfw]").is(":checked")) {
        $("#overlay, #overlay .popup.nsfw").addClass("show");
        return;
    }
    
    $("#main-content > form input[name=user]").val(token);
    
    $("#main-content > form").submit();
    
//    $.ajax({
//        url: "/pages/review-studio.php?vid-lang=" + $("input[name=vid-lang]").val() + "&vid-lang-name=" + $("input[name=vid-lang-name]").val(),
//        data: "user=" + googleUser.Id,
//    })
});
    
$("#overlay .popup.nsfw .buttons button.submit").click(function() {
    $("#overlay .popup.nsfw p.warning").remove();
    
    if(!checkCreateForm()) {
        $("<p class='warning'>There was an error with the selected options.</p>").insertBefore($("#overlay .popup.nsfw .buttons"));
        return;
    }
    
    $("#main-content > form input[name=user]").val(token);
    
    $("#main-content > form").submit();
});


$.fn.slideFadeToggle  = function(speed, easing, callback) {
        return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
};


$(".options .empty-space").hover(function() {
    $(this).find(".popup").toggleClass("show");
});