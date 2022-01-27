var app = angular.module('App');

app.controller('UserController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    var $ctrl = this;
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $scope.getUsers=function(){
        $http.get(geturl()+'api/users')
        .then(function(xhr){
            $scope.users = xhr.data;
        });
    }
    
    $scope.getUsers(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalUser = $uibModal.open({
            animation: true,
            templateUrl: 'modalUser.html',
            controller:'ModalUserCtrl', 
            scope: $scope
        })

        $scope.modalUser.result.then(function (result) {
             $scope.getUsers(); 
        }, function () {
        });
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.user ={};
        $scope.user.roles =[];
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/users/'+this.user.id)
        .then(function(xhr){
            $scope.user = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar al Usuario?',
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
                        $http.delete(geturl()+'api/users/'+_this.user.id) 
                        .then(function(xhr){
                            $scope.getUsers();
                            Notification.success({message:'Usuario eliminado correctamente!'});
                        });
                    }
                },
            }
        });
    }
});
 
app.controller('ModalUserCtrl', function ($http,$scope,$uibModalInstance,Notification,$ngConfirm) {
    $scope.init_roles=[];
    $scope.list_roles=[];
    $scope.validUsername = null;

    $http.get(geturl()+'api/roles')
    .then(function(xhr){
        $scope.init_roles = xhr.data;
        $scope.renderListRoles();
    });

    $scope.assignRol = function(){
        var i = $scope.init_roles.findIndex(p => p.id === parseInt($scope.rol_id));
        var index = $scope.user.roles.findIndex(p => p.id === parseInt($scope.rol_id));
        if(index == -1)
            $scope.user.roles.push($scope.init_roles[i]);
        $scope.renderListRoles();
    }

    $scope.removeRol = function(){
        var index = $scope.user.roles.findIndex(p => p.id === parseInt(this.rol.id));
        if(index != -1)
             $scope.user.roles.splice(index, 1)
    }

    $scope.renderListRoles=function(){
        $scope.list_roles=[];
        $scope.init_roles.forEach(function(v1,i1) {
            var index = $scope.user.roles.findIndex(p => p.id === parseInt(v1.id));
            if(index == -1)
                $scope.list_roles.push(v1);  
        });
    }

    $scope.validateUsername = function(){
        $http.post(geturl()+'api/users/validateUsername',{username:$scope.user.username})
        .then(function(xhr){
            $scope.validUsername = xhr.data.status;
            if(xhr.data.status)
                Notification.success({message:'Usuario valido!'});   
        });
    }

    $scope.save = function(){
        var data = {
            name:$scope.user.name,
            lastname:$scope.user.lastname,
            motherlastname:$scope.user.motherlastname,
            email:$scope.user.email,
            username:$scope.user.username,
            phone_mobile:$scope.user.phone_mobile,
            roles:$scope.user.roles
        }

        $http.post(geturl()+'api/users',data)  
        .then(function(xhr){
            $uibModalInstance.close('ok');

            $ngConfirm({
                title: 'Usuario creado correctament!',
                content: 'La contraseña asiganada es ' + xhr.data.password,
                scope: $scope,
                buttons: {
                    Si: {
                        text: 'Ok!',
                        btnClass: 'btn-default',
                        action: function(scope, button){

                        }
                    },
                }
            });
        });
    }

    $scope.update = function(){ 
        var data = {
            name:$scope.user.name,
            lastname:$scope.user.lastname,
            motherlastname:$scope.user.motherlastname,
            email:$scope.user.email,
            username:$scope.user.username,
            phone_mobile:$scope.user.phone_mobile,
            roles:$scope.user.roles
        }
        $http.put(geturl()+'api/users/'+$scope.user.id,data)
        .then(function(xhr){
            Notification.success({message:'Usuario actualizado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }
});