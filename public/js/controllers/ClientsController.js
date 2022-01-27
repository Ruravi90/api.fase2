var app = angular.module('App');
app.controller('ClientsController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getClients=function(){
        $http.get(geturl()+'api/clients')
        .then(function(xhr){
            $scope.clients = xhr.data;
        });
    }

    $scope.getClients(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalClient = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'modalClient.html',
            controller: 'ModalClientsCtrl',
            appendTo: parentElem,
            scope: $scope
        });

        $scope.modalClient.result
        .then(function(result){
            $scope.getClients();
        }).catch(function(result){

        }); 
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.client ={};
        $scope.client.reference_id = null;
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/clients/'+this.client.id)
        .then(function(xhr){
            $scope.client = xhr.data;
            if(xhr.data.address.length > 0){
                $scope.client.suburb = xhr.data.address[0].suburb;
                $scope.client.street = xhr.data.address[0].name;
                $scope.client.inner_number= xhr.data.address[0].inner_number;
                $scope.client.outdoor_number= xhr.data.address[0].outdoor_number;
                $scope.client.state= xhr.data.address[0].state;
                $scope.client.town= xhr.data.address[0].town;
                $scope.client.postal_code= xhr.data.address[0].postal_code;
                delete $scope.client.address;
            }

            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar al cliente?',
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
                        $http.delete(geturl()+'api/clients/'+_this.client.id) 
                        .then(function(xhr){
                            $scope.getClients();
                            Notification.success({message:'Cliente eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }
});


app.controller('ModalClientsCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $scope.select={};

    $http.get(geturl()+'api/cat_references') 
    .then(function(xhr){
        $scope.references = xhr.data;
        $scope.references.push({id:-1,name:'Otro'});
        if($scope.client != null)
            $scope.select.Reference = xhr.data.find(i=> i.id === parseInt($scope.client.reference_id));
    });

    $scope.save = function(form){
         if(form.$invalid){
            Notification.error({message:'Campos por llenar!'});
            return false;
        }
        var data = {
            name:$scope.client.name,
            lastname:$scope.client.lastname,
            motherlastname:$scope.client.motherlastname,
            email:$scope.client.email,
            phone_home:$scope.client.phone_home,
            phone_mobile:$scope.client.phone_mobile,
            suburb:$scope.client.suburb,
            street:"N/A",
            inner_number:0,
            outdoor_number:$scope.client.outdoor_number,
            state:$scope.client.state,
            town:$scope.client.town,
            postal_code:$scope.client.postal_code,
            reference_id:$scope.client.reference_id,
            other_ref:$scope.client.other_ref,
        }

        $http.post(geturl()+'api/clients',data) 
        .then(function(xhr){
            Notification.success({message:'Cliente creado correctamente!'});
            $uibModalInstance.close('add');
        });
    }

    $scope.update = function(form){

        if(form.$invalid){
            Notification.error({message:'Campos por llenar!'});
            return false;
        }

        var data = {
            name:$scope.client.name,
            lastname:$scope.client.lastname,
            motherlastname:$scope.client.motherlastname,
            email:$scope.client.email,
            phone_home:$scope.client.phone_home,
            phone_mobile:$scope.client.phone_mobile,
            suburb:$scope.client.suburb,
            street:"N/A",
            inner_number:0,
            outdoor_number:$scope.client.outdoor_number,
            state:$scope.client.state,
            town:$scope.client.town,
            postal_code:$scope.client.postal_code,
            reference_id:$scope.client.reference_id,
            other_ref:$scope.client.other_ref,
        }

        $http.put(geturl()+'api/clients/'+$scope.client.id,data)
        .then(function(xhr){
            Notification.success({message:'Cliente actualizado correctamente!'});
            $uibModalInstance.close('update');
        });
    }

    $scope.onSelectReference=function(item, model){
        $scope.client.reference_id = item.id;
    }
});