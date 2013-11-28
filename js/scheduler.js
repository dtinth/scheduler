
angular.module('scheduler', ['scheduler.editor'])
  .controller('MainController', function($scope) {
      
    $scope.editCourse = function(course) {
      $scope.data = course
      $('#edit-modal').modal('show')
    }
    
    $scope.addCourse = function() {
      var newCourse = {
        courseId: '',
        courseName: 'New Course',
        sections: [
          {
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
      // TODO: Use ajax.
      var value = JSON.stringify($scope.courses)
      var form = $('<form action="backend/save.php" method="post"></form>').appendTo('body')
      var input = $('<input type="hidden" name="data">').val(value).appendTo(form)
      form[0].submit()
    }
      
    $scope.courses = [
      {
        courseId: '01204111',
        courseName: 'Computers and Programming',
        sections: [
          {
            sectionNo: '450',
            instructor: 'Tyghe',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '17201' },
              { day: 5, start: '13:30', finish: '15:00', place: '0503' }
            ]
          },
          {
            sectionNo: '451',
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
        sections: [
          {
            sectionNo: '450',
            instructor: 'Tyghe',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '17201' },
              { day: 5, start: '13:30', finish: '15:00', place: '0503' }
            ]
          },
          {
            sectionNo: '451',
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
        sections: [
          {
            sectionNo: '450',
            instructor: 'Tyghe',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '17201' },
              { day: 5, start: '13:30', finish: '15:00', place: '0503' }
            ]
          },
          {
            sectionNo: '451',
            instructor: 'Siriporn',
            periods: [
              { day: 1, start: '13:00', finish: '14:30', place: '0202' },
              { day: 5, start: '13:30', finish: '15:00', place: '0203' }
            ]
          },
          {
            sectionNo: '452',
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