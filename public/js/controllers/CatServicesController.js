var app = angular.module('App');

app.controller('CatServicesController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getService=function(){
        $http.get(geturl()+'api/cat_services')
        .then(function(xhr){
            $scope.services = xhr.data;
        });
    }
    
    $scope.getService(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalService = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'modalService.html',
            controller: 'ModalCatServicesCtrl',
            appendTo: parentElem,
            scope: $scope
        });

        $scope.modalService.result
        .then(function(result){
            $scope.getService();
        }).catch(function(result){

        }); 
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.service ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/cat_services/'+this.service.id)
        .then(function(xhr){
            $scope.service = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar el tipo de servicio?',
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
                        $http.delete(geturl()+'api/cat_services/'+_this.service.id) 
                        .then(function(xhr){
                            $scope.getService();
                            Notification.success({message:'Servico eliminado correctamente!'});
                        });
                        
                    }
                },
            }
        });
    }
});

app.controller('ModalCatServicesCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $scope.save = function(){
        $http.post(geturl()+'api/cat_services',$scope.service) 
        .then(function(xhr){
            Notification.success({message:'Servicio creado correctamente!'});
            $uibModalInstance.close('add');
        });
    }

    $scope.update = function(){
        $http.put(geturl()+'api/cat_services/'+$scope.service.id,$scope.service)
        .then(function(xhr){
            Notification.success({message:'Servico actualizado correctamente!'});
            $uibModalInstance.close('update');
        });
    }
});