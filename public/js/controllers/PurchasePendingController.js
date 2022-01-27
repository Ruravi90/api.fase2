var app = angular.module('App');
app.controller('PurchasePendingController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification,blockUI) {
    $scope.isPaid = false;

    $scope.getPurchases=function(){
        $http.post(geturl()+'api/purchases_pending',{is_paid:$scope.isPaid})
        .then(function(xhr){
            $scope.purchases = xhr.data;
        });
    }

    $scope.getPurchases(); 


    $scope.update=function(){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de continuar?',
            scope: $scope,
            buttons: {
                No: {
                    text: 'Cancelar',
                    btnClass: 'btn-default',
                    action: function(scope, button){
                    }
                },
                Si: {
                    text: 'Si, confirmar!',
                    btnClass: 'btn-primary',
                    action: function(scope, button){
                        $http.put(geturl()+'api/purchases_pending/'+_this._purchase.id,{is_paid:!_this._purchase.is_paid})
                        .then(function(xhr){
                            $scope.getPurchases();
                        });
                    }
                },
            }
        });
    }
});