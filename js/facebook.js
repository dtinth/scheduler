
angular.module('facebook', ['ezfb'])
  .config(function ($FBProvider) {
    $FBProvider.setInitParams({
      appId: '239504039549087',
      status: true,
      cookie: true
    })
  })
  .service('facebook', function($FB) {
    
    var facebook = { }
    
    facebook.loggedIn = false
    
    facebook.login = function() {
      $FB.login()
    }
    
    facebook.logout = function() {
      $FB.logout()
    }
    
    function checkLoginStatus() {
      $FB.getLoginStatus().then(function(response) {
        var auth = response.authResponse
        if (!auth) {
          facebook.loggedIn = false
          facebook.auth = null
          facebook.me = null
        } else {
          facebook.auth = auth
          $FB.api('/me').then(function(me) {
            facebook.me = me
            facebook.loggedIn = true
          })
        }
      })
    }
    
    checkLoginStatus()
    $FB.Event.subscribe('auth.authResponseChange', function() {
      checkLoginStatus()
    })
    
    return facebook
    
  })