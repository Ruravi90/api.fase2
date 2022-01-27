var app = angular.module('App');
app.controller('InventoryController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getProviders=function(){
        $http.get(geturl()+'api/providers')
        .then(function(xhr){
            $scope.providers = xhr.data;
        });
    }

    $scope.getProviders(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalProvider = $uibModal.open({
            animation: true,
            templateUrl: 'modalProvider.html',
            controller:'ModalProviderCtrl', 
            scope: $scope
        })

        $scope.modalProvider.result.then(function (result) {
        }, function () {
        });
    }

    function formModal (scope,uibModalInstance, items){

    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.provider ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/providers/'+this.provider.id)
        .then(function(xhr){
            $scope.provider = xhr.data;
            if(xhr.data.address.length > 0){
                $scope.provider.street = xhr.data.address[0].name;
                $scope.provider.inner_number= xhr.data.address[0].inner_number;
                $scope.provider.outdoor_number= xhr.data.address[0].outdoor_number;
                $scope.provider.state= xhr.data.address[0].state;
                $scope.provider.town= xhr.data.address[0].town;
                $scope.provider.postal_code= xhr.data.address[0].postal_code;
                delete $scope.provider.address;
            }
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar al Proveedor?',
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
                        $http.delete(geturl()+'api/providers/'+_this.provider.id) 
                        .then(function(xhr){
                            $scope.getProviders();
                            Notification.success({message:'Proveedor eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }
});

app.controller('ModalProviderCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $scope.save = function(){
        var data = {
            business_name:$scope.provider.business_name,
            contact_name:$scope.provider.contact_name,
            office_phone:$scope.provider.office_phone,
            email:$scope.provider.email,
            street:$scope.provider.street,
            inner_number:$scope.provider.inner_number,
            outdoor_number:$scope.provider.outdoor_number,
            state:$scope.provider.state,
            town:$scope.provider.town,
            postal_code:$scope.provider.postal_code
        }

        $http.post(geturl()+'api/providers',data)  
        .then(function(xhr){
            Notification.success({message:'Proveedor creado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.update = function(){ 
        var data = {
            business_name:$scope.provider.business_name,
            contact_name:$scope.provider.contact_name,
            office_phone:$scope.provider.office_phone,
            email:$scope.provider.email,
            street:$scope.provider.street,
            inner_number:$scope.provider.inner_number,
            outdoor_number:$scope.provider.outdoor_number,
            state:$scope.provider.state,
            town:$scope.provider.town,
            postal_code:$scope.provider.postal_code
        }

        $http.put(geturl()+'api/providers/'+$scope.provider.id,data)
        .then(function(xhr){
            Notification.success({message:'Proveedor actualizado correctamente!'});
            $uibModalInstance.close('ok');
            $scope.getProviders(); 
        });
    }
});