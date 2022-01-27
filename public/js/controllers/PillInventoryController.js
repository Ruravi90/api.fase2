var app = angular.module('App');

app.controller('PillInventoryController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    var $ctrl = this;
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getInventory=function(){
        $http.get(geturl()+'api/pills_inventory')
        .then(function(xhr){
            $scope.inventory = xhr.data;
        });
    }
    
    $scope.getInventory(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalUser = $uibModal.open({
            animation: true,
            templateUrl: 'modalInventory.html',
            controller:'ModalInventoryCtrl', 
            scope: $scope
        })

        $scope.modalUser.result.then(function (result) {
             $scope.getInventory(); 
        }, function () {
        });
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.pill ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/pills_inventory/'+this.pill.id)
        .then(function(xhr){
            $scope.pill = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar el registro del inventario?',
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
                    btnClass: 'btn-danger',
                    action: function(scope, button){
                        $http.delete(geturl()+'api/pills_inventory/'+_this.pill.id) 
                        .then(function(xhr){
                            $scope.getInventory();
                            Notification.success({message:'Registro eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }
});
 
app.controller('ModalInventoryCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $http.get(geturl()+'api/cat_pills')
    .then(function(xhr){
        $scope.catPills = xhr.data;
    });

    $scope.save = function(){
        var data = {
            pill_id:$scope.pill.pill_id,
            count:$scope.pill.count
        }

        $http.post(geturl()+'api/pills_inventory',data)  
        .then(function(xhr){
            Notification.success({message:'Registro creado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.update = function(){ 
        var data = {
            pill_id:$scope.pill.pill_id,
            count:$scope.pill.count
        }
        $http.put(geturl()+'api/pills_inventory/'+$scope.pill.id,data)
        .then(function(xhr){
            Notification.success({message:'Registro actualizado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }
});