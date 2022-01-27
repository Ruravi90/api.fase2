
function geturl(){
    return window.location.origin+'/';
}

var app = angular.module('App', ['ui.bootstrap','cp.ngConfirm','ui-notification', 'ui.bootstrap.datetimepicker','scania.angular.select2','ngSanitize', 'ui.select','ngMask','blockUI'], function($interpolateProvider) {
	$interpolateProvider.startSymbol('<%');
	$interpolateProvider.endSymbol('%>');
});

app.config(function($httpProvider,NotificationProvider,$qProvider,blockUIConfig) {
    NotificationProvider.setOptions({
        delay: 9000,
        startTop: 20,
        startRight: 10,
        verticalSpacing: 20,
        horizontalSpacing: 20,
        positionX: 'right',
        positionY: 'bottom'
    });

    blockUIConfig.message = 'Trabajando...';

    //$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
    //$httpProvider.defaults.headers.post['Accept'] = 'application/x-www-form-urlencoded';
});

app.run(function($rootScope,$http, $templateCache) {
  
});

app.controller('MainController', function($rootScope,$scope, $http,Notification) {
    $rootScope.anySearch='';
});

app.directive('capitalize', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, modelCtrl) {
        var capitalize = function(inputValue) {
          if (inputValue == undefined) inputValue = '';
          var capitalized = inputValue.toUpperCase();
          if (capitalized !== inputValue) {
            // see where the cursor is before the update so that we can set it back
            var selection = element[0].selectionStart;
            modelCtrl.$setViewValue(capitalized);
            modelCtrl.$render();
            // set back the cursor after rendering
            element[0].selectionStart = selection;
            element[0].selectionEnd = selection;
          }
          return capitalized;
        }
        modelCtrl.$parsers.push(capitalize);
        capitalize(scope[attrs.ngModel]); // capitalize initial value
      }
    };
  });
