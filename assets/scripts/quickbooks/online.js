var OAuthCode = function(qb_url) {
    this.loginPopup = function (parameter) {
        this.loginPopupUri(parameter);
    };

    this.loginPopupUri = function (parameter) {

        // Launch Popup
        var parameters = "location=1,width=800,height=650";
        parameters += ",left=" + (screen.width - 800) / 2 + ",top=" + (screen.height - 650) / 2;

        var win = window.open(qb_url, 'connectPopup', parameters);
        var pollOAuth = window.setInterval(function () {
            try {

                if (win.document.URL.indexOf("code") != -1) {
                    window.clearInterval(pollOAuth);
                    win.close();
                    location.reload();
                }
            } catch (e) {
                console.log(e)
            }
        }, 100);
    }
};

var apiCall = function() {
    this.getCompanyInfo = function() {
        $.ajax({
            type: "GET",
            url: "apiCall.php",
        }).done(function( msg ) {
            $( '#apiCall' ).html( msg );
        });
    }

    this.refreshToken = function() {
        $.ajax({
            type: "POST",
            url: "refreshToken.php",
        }).done(function( msg ) {

        });
    }
};

var oauth = new OAuthCode(qb_url);
var apiCall = new apiCall();