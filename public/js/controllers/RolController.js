var app = angular.module('App');

app.controller('RolController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getRoles=function(){
        $http.get(geturl()+'api/roles')
        .then(function(xhr){
            $scope.roles = xhr.data;
        });
    }

    $scope.getRoles(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalRol = $uibModal.open({
            animation: true,
            templateUrl: 'modalRol.html',
            controller:'ModalRolCtrl', 
            scope: $scope
        })

        $scope.modalRol.result.then(function (result) {
             $scope.getRoles(); 
        }, function () {
        });
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.rol ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/roles/'+this.rol.id)
        .then(function(xhr){
            $scope.rol = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar el rol?',
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
                        $http.delete(geturl()+'api/roles/'+_this.rol.id) 
                        .then(function(xhr){
                            $scope.getRoles();
                            Notification.success({message:'Rol eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }

    // Permissons 
    // 
    
    $scope.getPermissions=function(){
        $http.get(geturl()+'api/permissions')
        .then(function(xhr){
            $scope.permissions = xhr.data;
        });
    }

    $scope.getPermissions();

    $scope.showModalPermission = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalPermission = $uibModal.open({
            animation: true,
            templateUrl: 'modalPermission.html',
            controller:'ModalPermissionCtrl', 
            scope: $scope
        })

        $scope.modalPermission.result.then(function (result) {
             $scope.getPermissions(); 
        }, function () {
        });
    }

    $scope.addPermission = function(){
        $scope.IsEdit =false;
        $scope.permission ={};
        $scope.showModalPermission();
    }

    $scope.editPermission = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/permissions/'+this.permission.id)
        .then(function(xhr){
            $scope.permission = xhr.data;
            $scope.showModalPermission();
        }); 
    }

    $scope.deletePermission = function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar al permiso?',
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
                        $http.delete(geturl()+'api/permissions/'+_this.permission.id) 
                        .then(function(xhr){
                            $scope.getPermissions();
                            Notification.success({message:'Permisso eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }
});
 
app.controller('ModalRolCtrl', function ($http,$scope,$uibModalInstance,Notification) {
    $scope.list_permissions = [];

    if(!$scope.$parent.IsEdit){
        $scope.rol = {} ;
        $scope.rol.permissions = [];
    }
    else
        $scope.rol = $scope.$parent.rol;

    $http.get(geturl()+'api/permissions')
    .then(function(xhr){
        $scope.init_permissions = xhr.data;
        $scope.renderListPermissons();
    });

    $scope.save = function(){
        var data = {
            name:$scope.rol.name,
            slug:$scope.rol.slug,
            description:$scope.rol.description,
            permissions:$scope.rol.permissions,
        }

        $http.post(geturl()+'api/roles',data)  
        .then(function(xhr){
            Notification.success({message:'Rol creado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.update = function(){ 
        var data = {
            name:$scope.rol.name,
            slug:$scope.rol.slug,
            description:$scope.rol.description,
            permissions:$scope.rol.permissions,
        }
        $http.put(geturl()+'api/roles/'+$scope.rol.id,data)
        .then(function(xhr){
            Notification.success({message:'Rol actualizado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.assignPermisson = function(){
        var i = $scope.init_permissions.findIndex(p => p.id === parseInt($scope.permissions_id));
        var index = $scope.rol.permissions.findIndex(p => p.id === parseInt($scope.permissions_id));
        if(index == -1)
            $scope.rol.permissions.push($scope.init_permissions[i]);
        $scope.renderListPermissons();
    }

    $scope.removePermison = function(){
        var index = $scope.rol.permissions.findIndex(p => p.id === parseInt(this.permission.id));
        if(index != -1)
             $scope.rol.permissions.splice(index, 1)
    }

    $scope.renderListPermissons=function(){
        $scope.list_permissions=[];
        $scope.init_permissions.forEach(function(v1,i1) {
            var index =  $scope.rol.permissions.findIndex(p => p.id === parseInt(v1.id));
            if(index == -1)
                $scope.list_permissions.push(v1);    
        });
    }
});


app.controller('ModalPermissionCtrl', function ($http,$scope,$uibModalInstance,Notification) {

    $scope.save = function(){
        var data = {
            name:$scope.permission.name,
            slug:$scope.permission.slug,
            description:$scope.permission.description,
        }

        $http.post(geturl()+'api/permissions',data)  
        .then(function(xhr){
            Notification.success({message:'Permiso creado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.update = function(){ 
        var data = {
            name:$scope.permission.name,
            slug:$scope.permission.slug,
            description:$scope.permission.description,
        }
        $http.put(geturl()+'api/permissions/'+$scope.permission.id,data)
        .then(function(xhr){
            Notification.success({message:'Permiso actualizado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }
});