

angular.module('scheduler.import', ['ui.utils'])
  .controller('ImportController', function($scope, $http, $sce, $q) {
    
    var all = []
    
    $scope.searchTerm = ''
    $scope.semester = '1'
    
    $scope.loading = true
    $scope.loaded = false
    $scope.results = null // null : no input, [] : no match
    
    $http.get('backend/ku/course_list.php').success(function(result) {
      result.forEach(function(course) {
        course.base = (course.id + ' ' + course.name).toLowerCase()
      })
      all = result
      $scope.loaded = true
    }).error(function(e) {
      $scope.importError = 'Cannot get the course list from KU'
    }).finally(function() {
      $scope.loading = false
    })
    
    $scope.$watch('searchTerm', function(value) {
      if (value == '') {
        $scope.results = null
      } else {
        $scope.results = all.filter(checkCourse(value))
        $scope.results.sort(function(a, b) {
          return b.score - a.score
        })
        if ($scope.results.length > 10) $scope.results.length = 10
      }
    })
    
    function fuzzyMatch(pattern) {
      return function matcher(subject) {
        var startIndex = 0
        var results = [ ]
        for (var i = 0; i < pattern.length; i ++) {
          var index = subject.indexOf(pattern.charAt(i), startIndex)
          if (index == -1) return false
          results.push(index)
          startIndex = index + 1
        }
        return results
      }
    }
    
    function checkCourse(searchTerm) {
      searchTerm = searchTerm.toLowerCase()
      var match = fuzzyMatch(searchTerm)
      return function filterer(course) {
        var result = match(course.base)
        if (!result) return false
        course.score = calculateScore(result)
        course.result = result
        return true
      }
    }
    
    $scope.highlight = function(course, field, start) {
      var html = ''
      var name = course[field]
      var result = course.result
      var state = false // true - highlighting, false = no highlight
      for (var i = 0; i < name.length; i ++) {
        var nextState = result.indexOf(i + start) >= 0
        if (!state && nextState) html += '<b>'
        if (state && !nextState) html += '</b>'
        html += name.charAt(i)
        state = nextState
      }
      if (state) html += '</b>'
      return $sce.trustAsHtml(html)
    }
    
    function calculateScore(indices) {
      var score = 0
      for (var i = 0; i < indices.length; i ++) {
        score += Math.exp(-indices[i])
      }
      return score
    }
    
    
    // import
    
    $scope.importCourse = function(course) {
      $scope.importing = true
      var url = 'backend/ku/import.php?id=' + course.id + '&semester=' + $scope.semester
      $http.get(url).success(function(courseTimetable) {
        $scope.importing = false
        $scope.importTimetable(courseTimetable)
      }).error(function(wtf) {
        $scope.importError = 'Cannot import section. Please try again later.'
      })
    }
    
  })
  .controller('ImportTestController', function($scope) {
    
    $scope.importTimetable = function(timetable) {
      console.log(timetable)
    }
    
  })