
angular.module('scheduler', ['scheduler.editor', 'scheduler.import',  'scheduler.view', 'ezfb'])
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
  .controller('MainController', function($scope, facebook, schedules) {
    
    $scope.facebook = facebook
    
    $scope.editCourse = function(course) {
      $scope.data = course
      $('#edit-modal').modal('show')
    }
    
    $scope.addCourse = function() {
      var newCourse = {
        courseId: '',
        courseName: 'New Course',
        lecCredit:'1', labCredit:'1',
        sections: [
          {
            type:'0',
            sectionNo: '1',
            instructor: '',
            periods: [
              { day: 1, start: '', finish: '', place: '' }
            ]
          }
        ]
      };
      $scope.courses.push(newCourse)
      $scope.editCourse(newCourse)
    }
    
    $scope.saveSchedule = function() {
      $('#save-modal').modal('show')
    }
    
    $scope.importFromKU = function() {
      $('#ku-import-modal').modal('show')
    }
    
    $scope.importTimetable = function(timetable) {
      $('#ku-import-modal').modal('hide')
      $scope.courses.push(timetable)
    }
      
    $scope.courses = [ ]
    
    try {
      schedules.urlToScope($scope)
    } catch (e) {
      // no ID given
    }
    
  })
  .controller('SaveController', function($scope, $http) {
    
    $scope.info = { name: '' }
    
    $scope.save = function() {
      var data = { name: $scope.info.name, courses: $scope.courses }
      $scope.saving = true
      $http.post('backend/save.php', data)
        .success(function(result) {
          location.href = ('view.html?id=' + result.key)
        })
        .error(function() {
          alert('Cannot save schedule! Sorry')
        })
        .finally(function() {
          $scope.saving = false
        })
    }
    
  })