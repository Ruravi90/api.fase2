var app = angular.module('App');

app.controller('ProductInventoryController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    var $ctrl = this;
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getInventory=function(){
        $http.get(geturl()+'api/products_inventory')
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
        $scope.product ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/products_inventory/'+this.product.id)
        .then(function(xhr){
            $scope.product = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar el producto del inventario?',
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
                        $http.delete(geturl()+'api/products_inventory/'+_this.product.id) 
                        .then(function(xhr){
                            $scope.getInventory();
                            Notification.success({message:'Producto eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }
});
 
app.controller('ModalInventoryCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $http.get(geturl()+'api/cat_products')
    .then(function(xhr){
        $scope.catProducts = xhr.data;
    });

    $scope.save = function(){
        var data = {
            product_id:$scope.product.product_id,
            count:$scope.product.count
        }

        $http.post(geturl()+'api/products_inventory',data)  
        .then(function(xhr){
            Notification.success({message:'Producto creado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.update = function(){ 
        var data = {
            product_id:$scope.product.product_id,
            count:$scope.product.count
        }
        $http.put(geturl()+'api/users/'+$scope.product.id,data)
        .then(function(xhr){
            Notification.success({message:'Producto actualizado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }
});