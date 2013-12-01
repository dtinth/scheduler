
angular.module('scheduler.view', ['ui.utils'])
  .factory('schedules', function($http) {
    var schedules = { }
    schedules.getIdFromUrl = function() {
      var match = location.search.match(/id=([0-9a-f]+)/)
      if (!match) {
        throw new Error('No ID')
      }
      return match[1]
    }
    schedules.load = function(id) {
      return $http.get('backend/load.php?id=' + id)
    }
    schedules.urlToScope = function($scope) {
      schedules.load(schedules.getIdFromUrl())
        .success(function(data) {
          $scope.courses = data.courses
          $scope.scheduleInfo = data
        })
        .error(function() {
          alert('fail')
        })
    }
    return schedules
  })
  .controller('ViewPageController', function($scope, schedules) {
    schedules.urlToScope($scope)
  })
  .controller('ScheduleViewController', function($scope) {
    
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
    
  })
  .controller('ScheduleTimetableViewController', function($scope) {
    
    var rowHeight = 36
    var gridHeight = 18
    
    var hourStart = 8
    var hourFinish = 16
    
    $scope.height = rowHeight * 7 + gridHeight
    $scope.days = [
      { text: "M", day: 1, y: 0 * rowHeight + gridHeight, h: rowHeight },
      { text: "T", day: 2, y: 1 * rowHeight + gridHeight, h: rowHeight },
      { text: "W", day: 3, y: 2 * rowHeight + gridHeight, h: rowHeight },
      { text: "T", day: 4, y: 3 * rowHeight + gridHeight, h: rowHeight },
      { text: "F", day: 5, y: 4 * rowHeight + gridHeight, h: rowHeight },
      { text: "S", day: 6, y: 5 * rowHeight + gridHeight, h: rowHeight },
      { text: "S", day: 0, y: 6 * rowHeight + gridHeight, h: rowHeight },
    ]
    $scope.gridlines = []
    
    $scope.$watch('courses', function() {
      render()
    }, true)
    
    function getDayIndex(day) {
      for (var i = 0; i < $scope.days.length; i ++) if ($scope.days[i].day == day) return i
    }
    
    function toHours(text) {
      text = ('' + text).replace(/\D/g, '')
      if (text.length != 4) return null
      return text.substr(0, 2) / 1 + text.substr(2) / 60
    }
    
    function x(hour) {
      return (hour - hourStart) / (hourFinish - hourStart)
    }
    
    function render() {
      
      if (!$scope.courses) return
      
      var items = [ ]
      
      $scope.days.forEach(function(day) {
        day.items = []
      })
      
      $scope.courses.forEach(function(course) {
        course.sections.forEach(function(section) {
          if (!section.selected && !section.preview) return
          section.periods.forEach(function(period) {
            var start = toHours(period.start)
            var finish = toHours(period.finish)
            var day = +period.day
            if (start != null && finish != null && finish > start && 0 <= day && day < 7) {
              var item = {
                start: start,
                finish: finish,
                course: course,
                section: section,
                preview: section.preview && !section.selected,
                hover: section.preview,
                period: period
              }
              items.push(item)
              $scope.days[getDayIndex(day)].items.push(item)
            }
          })
        })
      })
      
      
      var cy = gridHeight
      
      $scope.days.forEach(function(day) {
        var using = [false]
        var events = []
        day.items.forEach(function(item) {
          if (item.preview) {
            item.row = 0
            return
          }
          events.push({ type: 'start', item: item, time: item.start })
          events.push({ type: 'finish', item: item, time: item.finish - 0.00000001 })
        })
        events.sort(function(a, b) {
          return a.time - b.time
        })
        events.forEach(function(event) {
          if (event.type == 'start') {
            for (var i = 0; using[i]; i ++) { } // i = first free row
            using[i] = true
            event.item.row = i
          } else if (event.type == 'finish') {
            using[event.item.row] = false
          }
        })
        day.rows = using.length
        day.y = cy
        cy += day.h = day.rows * rowHeight
      })
      
      $scope.height = cy
      
      function notPreview(item) {
        return !item.preview
      }
      
      var startHours = items.filter(notPreview).map(function(item) { return item.start })
      var finishHours = items.filter(notPreview).map(function(item) { return item.finish })
      
      hourStart = Math.min(Math.floor(Math.min.apply(Math, startHours) - 1), 9)
      hourFinish = Math.max(Math.ceil(Math.max.apply(Math, finishHours) + 1), 16)
      
      items.forEach(function(item) {
        item.x = x(item.start) * 100 + '%'
        item.w = (x(item.finish) - x(item.start)) * 100 + '%'
        item.y = rowHeight * item.row
        item.h = rowHeight
      })
      
      $scope.gridlines = []
      
      for (var i = hourStart; i <= hourFinish; i ++) {
        $scope.gridlines.push({ hour: i, x: x(i) * 100 + '%' })
      }
      
    }
    
  })