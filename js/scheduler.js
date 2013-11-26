
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
    
    $scope.date = function(day){
      if(day==0)return "Sunday"
      if(day==1)return "Monday"
      if(day==2)return "Tuesday"
      if(day==3)return "Wednesday"
      if(day==4)return "Thursday"
      if(day==5)return "Friday"
      if(day==6)return "Saturday"
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
            ]
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
            ]
          }
        ]
      }
    ]
  })