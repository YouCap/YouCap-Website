var authInstance;

var onSignedIn;
var onNotSignedIn;
var onLogout;
var profile;

async function loadAuthInstance() {
    const load = new Promise(function(resolve, reject) {
        gapi.load('auth2', resolve);
    });
    return load.then(async () => {
        return await window.gapi.auth2
            .init({
                client_id: "244600529413-0ari1hd8rrkkgk7696nbb4tg2bea4hii.apps.googleusercontent.com",
            })
            .then(auth2 => {
                authInstance = auth2;
                
                if(authInstance.isSignedIn.get())
                    onSignIn(authInstance.currentUser.get());
                else if(onNotSignedIn)
                    onNotSignedIn();
            });
    });
}

loadAuthInstance();

function onSignIn(googleUser) {
    profile = googleUser.getBasicProfile();
    
    var userInfo = {};
    userInfo.Name = profile.getName();
    userInfo.Email = profile.getEmail()
        
    if(onSignedIn)
        onSignedIn();
}

function isLoggedIn() {
    console.log(authInstance);

    return authInstance.isSignedIn().get();
}

function logout() {
    authInstance.signOut();
}