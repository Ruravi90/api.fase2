var app = angular.module('App');

app.controller('DepartmentController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    var $ctrl = this;
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getDepartments=function(){
        $http.get(geturl()+'api/departments')
        .then(function(xhr){
            $scope.departments = xhr.data;
        });
    }
    
    $scope.getDepartments(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalDepartment = $uibModal.open({
            animation: true,
            templateUrl: 'modalDepartment.html',
            controller:'ModalDepartmentCtrl', 
            scope: $scope
        })

        $scope.modalDepartment.result.then(function (result) {
             $scope.getDepartments(); 
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
        $http.get(geturl()+'api/departments/'+this.department.id)
        .then(function(xhr){
            $scope.department = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar el registro?',
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
                        $http.delete(geturl()+'api/departments/'+_this.department.id) 
                        .then(function(xhr){
                            $scope.getDepartments(); 
                            Notification.success({message:'Registro eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }
});
 
app.controller('ModalDepartmentCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $scope.save = function(){
        $http.post(geturl()+'api/departments',$scope.department)  
        .then(function(xhr){
            Notification.success({message:'Registro creado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.update = function(){ 
        $http.put(geturl()+'api/departments/'+$scope.department.id,$scope.department)
        .then(function(xhr){
            Notification.success({message:'Registro actualizado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }
});