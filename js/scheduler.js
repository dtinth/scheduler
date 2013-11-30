
angular.module('scheduler', ['scheduler.editor', 'ezfb'])
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
  .controller('MainController', function($scope, facebook) {
    
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
    
    $scope.activeSections = function(sections) {
      return sections.filter(function(section) {
        return section.selected
      })
    }
    
    $scope.date = function(day){
      return ["Sunday", "Monday", "Tuesday",
              "Wednesday", "Thursday", "Friday", "Saturday"][day]
    }
    
    $scope.time = function(time) {
      return ('' + time).replace(/[^\d]/g, '').replace(/^(\d\d)/, '$1:')
    }
    
    $scope.saveSchedule = function() {
      $('#save-modal').modal('show')
    }
      
    $scope.courses = [
      {
        courseId: '01204111',
        courseName: 'Computers and Programming',
        lecCredit:'1', labCredit:'2',
        sections: [
          {
            sectionNo: '450',
            type:'0',
            instructor: 'Tyghe',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '17201' },
              { day: 5, start: '13:30', finish: '15:00', place: '0503' }
            ]
          },
          {
            sectionNo: '451', credit:'1',
            type:'1',
            instructor: 'Siriporn',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '0202' },
              { day: 5, start: '13:30', finish: '15:00', place: '0203' }
            ],
            selected: true
          }
        ]
      },
      {
        courseId: '01204112',
        courseName: 'Computers and Programming XXX',
        lecCredit:'1', labCredit:'2',
        sections: [
          {
            sectionNo: '450',
            type:'0',
            instructor: 'Tyghe',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '17201' },
              { day: 5, start: '13:30', finish: '15:00', place: '0503' }
            ]
          },
          {
            sectionNo: '451',
            type:'1',
            instructor: 'Siriporn',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '0202' },
              { day: 5, start: '13:30', finish: '15:00', place: '0203' }
            ]
          }
        ]
      },
      {
        courseId: '01204113',
        courseName: 'Computers and Programming YYY',
        lecCredit:'1', labCredit:'2',
        sections: [
          {
            sectionNo: '450',
            type:'0',
            instructor: 'Tyghe',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '17201' },
              { day: 5, start: '13:30', finish: '15:00', place: '0503' }
            ]
          },
          {
            sectionNo: '451',
            type:'1',
            instructor: 'Siriporn',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '0202' },
              { day: 5, start: '13:30', finish: '15:00', place: '0203' }
            ]
          },
          {
            sectionNo: '452',
            type:'1',
            instructor: 'Siriporn',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '0202' },
              { day: 5, start: '13:30', finish: '15:00', place: '0203' }
            ],
            selected: true
          }
        ]
      }
    ]
  })
  .controller('SaveController', function($scope, $http) {
    
    $scope.info = { name: '' }
    
    $scope.save = function() {
      var data = { name: $scope.info.name, courses: $scope.courses }
      $scope.saving = true
      $http.post('backend/save.php', data)
        .success(function(result) {
          location.replace('?id=' + result.key)
        })
        .error(function() {
          alert('Cannot save schedule! Sorry')
        })
        .finally(function() {
          $scope.saving = false
        })
    }
    
  })