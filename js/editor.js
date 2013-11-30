
angular.module('scheduler.editor', ['ui.utils'])
  .controller('SectionController', function($scope) {
    $scope.addPeriod = function() {
      $scope.section.periods.push({
        day: 1, start: '', finish: '', place: ''
      })
    }
    $scope.removePeriod = function(period) {
      $scope.section.periods.splice($scope.section.periods.indexOf(period), 1)
    }
    $scope.removeSection = function(section) {
      $scope.data.sections.splice($scope.data.sections.indexOf(section), 1)
    }
  })
  .controller('EditorController', function($scope) {
    $scope.addSection = function() {
      $scope.data.sections.push({
        sectionNo: '1',
        type:'0',
        instructor: '',
        periods: [
          { day: 1, start: '', finish: '', place: '' }
        ]
      })
    }
    $scope.removeCourse = function(course) {
      $scope.courses.splice($scope.courses.indexOf(course),1)
    }
  })
  .controller('EditorTestController', function($scope) {
    $scope.data = {
      courseId: '01204111',
      courseName: 'Computers and Programming',
      lecCredit:'1', labCredit:'1',
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
    }
  })

