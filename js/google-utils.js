var authInstance;

var onSignedIn;
var onLogout;

function onSignIn(googleUser) {
    var profile = googleUser.getBasicProfile();
    
    var userInfo = {};
    userInfo.Name = profile.getName();
    userInfo.Email = profile.getEmail()
    
    sessionStorage.setItem("googleUser", JSON.stringify(userInfo));
    
    if(onSignedIn)
        onSignedIn();
}

function isLoggedIn() {
    return sessionStorage.getItem('googleUser') != null;
}

function logout() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut();
    sessionStorage.clear();
}

gapi.load("auth2", function() {
    var auth2 = gapi.auth2.init({
        client_id : "244600529413-0ari1hd8rrkkgk7696nbb4tg2bea4hii.apps.googleusercontent.com"
    });

    authInstance = gapi.auth2.getAuthInstance();
});