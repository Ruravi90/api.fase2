var app = angular.module('App');

app.controller('CreditorController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getCreditors=function(){
        $http.get(geturl()+'api/creditors')
        .then(function(xhr){
            $scope.creditors = xhr.data;
        });
    }
    
    $scope.getCreditors(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalCreditor = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body', 
            templateUrl: 'modalCreditor.html',
            controller: 'ModalCreditorCtrl',
            appendTo: parentElem,
            scope: $scope
        });

        $scope.modalCreditor.result
        .then(function(result){
            $scope.getCreditors();
        }).catch(function(result){

        }); 
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.creditor ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/creditors/'+this.creditor.id)
        .then(function(xhr){
            $scope.creditor = xhr.data;
            if(xhr.data.address.length > 0){
                $scope.creditor.street = xhr.data.address[0].name;
                $scope.creditor.inner_number= xhr.data.address[0].inner_number;
                $scope.creditor.outdoor_number= xhr.data.address[0].outdoor_number;
                $scope.creditor.state= xhr.data.address[0].state;
                $scope.creditor.town= xhr.data.address[0].town;
                $scope.creditor.postal_code= xhr.data.address[0].postal_code;
                delete $scope.creditor.address;
            }
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar al acreedor?',
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
                        $http.delete(geturl()+'api/creditors/'+_this.creditor.id) 
                        .then(function(xhr){
                            $scope.getCreditors();
                            Notification.success({message:'Acreedor eliminado correctamente!'});
                        });
                        
                    }
                },
            }
        });
    }
});

app.controller('ModalCreditorCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $scope.save = function(){
        var data = {
            business_name:$scope.creditor.business_name,
            contact_name:$scope.creditor.contact_name,
            office_phone:$scope.creditor.office_phone,
            email:$scope.creditor.email,
            street:$scope.creditor.street,
            inner_number:$scope.creditor.inner_number,
            outdoor_number:$scope.creditor.outdoor_number,
            state:$scope.creditor.state,
            town:$scope.creditor.town,
            postal_code:$scope.creditor.postal_code
        }

        $http.post(geturl()+'api/creditors',data)  
        .then(function(xhr){
            Notification.success({message:'Acreedor creado correctamente!'});
            $uibModalInstance.close('add');
        });
    }

    $scope.update = function(){
        var data = {
            business_name:$scope.creditor.business_name,
            contact_name:$scope.creditor.contact_name,
            office_phone:$scope.creditor.office_phone,
            email:$scope.creditor.email,
            street:$scope.creditor.street,
            inner_number:$scope.creditor.inner_number,
            outdoor_number:$scope.creditor.outdoor_number,
            state:$scope.creditor.state,
            town:$scope.creditor.town,
            postal_code:$scope.creditor.postal_code
        }
        $http.put(geturl()+'api/creditors/'+$scope.creditor.id,data) 
        .then(function(xhr){
            Notification.success({message:'Acreedor actualizado correctamente!'});
            $uibModalInstance.close('update');
        });
    }
});