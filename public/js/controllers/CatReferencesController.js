var app = angular.module('App');
app.controller('CatReferencesController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getReferences=function(){
        $http.get(geturl()+'api/cat_references')
        .then(function(xhr){
            $scope.references = xhr.data;
        });
    }
    
    $scope.getReferences(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalReference = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'modalReference.html',
            controller: 'ModalCatReferencesCtrl',
            appendTo: parentElem,
            scope: $scope
        });

        $scope.modalReference.result
        .then(function(result){
            $scope.getReferences();
        }).catch(function(result){

        }); 
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.reference ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/cat_references/'+this.referent.id)
        .then(function(xhr){
            $scope.reference = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar la referencia?',
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
                        $http.delete(geturl()+'api/cat_references/'+_this.referent.id) 
                        .then(function(xhr){
                            $scope.getReferences();
                            Notification.success({message:'Referencia eliminado correctamente!'});
                        });
                        
                    }
                },
            }
        });
    }
});

app.controller('ModalCatReferencesCtrl', function ($http,$scope,$uibModalInstance,Notification) {
        $scope.save = function(){ 
        var data = {
            name:$scope.reference.name
        }

        $http.post(geturl()+'api/cat_references',data) 
        .then(function(xhr){
            Notification.success({message:'Referencia creada correctamente!'});
            $uibModalInstance.close('add');
        });
    }

    $scope.update = function(){
        var data = {
            name:$scope.reference.name
        }

        $http.put(geturl()+'api/cat_references/'+$scope.reference.id,data)
        .then(function(xhr){
            Notification.success({message:'Referencia actualizado correctamente!'});
            $uibModalInstance.close('update');
        });
    }
});