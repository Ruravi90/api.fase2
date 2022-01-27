var app = angular.module('App');

app.controller('BoxController', function($rootScope, $scope, $timeout,$http, $document, $uibModal, $ngConfirm, Notification) {
    $scope.balances = [];
    $scope.getPackages = function() {
        $http.get(geturl() + '/api/box/balance').then(function(xhr) {
            $scope.balances = xhr.data;
        });
    }

    $timeout(function(){
        $scope.getPackages();
    },500*10)

});
