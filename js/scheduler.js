
angular.module('scheduler', ['scheduler.editor', 'scheduler.import',  'scheduler.view', 'ezfb', 'facebook'])
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
  .controller('SaveController', function($scope, $http, schedules) {
    
    $scope.info = { name: '' }
    
    $scope.$watch('scheduleInfo', function(info) {
      if (info) $scope.info.name = info.name
    })
    
    $scope.save = function() {
      var data = { name: $scope.info.name, courses: $scope.courses }
      var id = $scope.scheduleId
      if (id) data.id = id
      $scope.saving = true
      $http.post('backend/save.php', data)
        .success(function(result) {
          location.href = ('view.php?id=' + result.key)
        })
        .error(function() {
          alert('Cannot save schedule! Sorry')
        })
        .finally(function() {
          $scope.saving = false
        })
    }
    
  })