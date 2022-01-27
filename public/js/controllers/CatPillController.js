var app = angular.module('App');
app.controller('CatPillController', function($rootScope, $scope, $http, $document, $uibModal, $ngConfirm, Notification) {
    $scope.getPills = function() {
        $http.get(geturl() + 'api/cat_pills').then(function(xhr) {
            $scope.pills = xhr.data;
        });
    }
    $scope.getPills();
    $scope.showModal = function() {
        var parentElem = angular.element($document[0].querySelector('body'));
        $scope.modalPill = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'modalPill.html',
            controller: 'ModalCatPillCtrl',
            appendTo: parentElem,
            scope: $scope
        });
        $scope.modalPill.result.then(function(result) {
            $scope.getPills();
        }).catch(function(error) {});
    }
    $scope.add = function() {
        $scope.IsEdit = false;
        $scope.pill = {};
        $scope.showModal();
    }
    $scope.edit = function() {
        $scope.IsEdit = true;
        $http.get(geturl() + 'api/cat_pills/' + this.pill.id).then(function(xhr) {
            $scope.pill = xhr.data;
            $scope.showModal();
        });
    }
    $scope.deleted = function(_token) {
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar la pastilla?',
            scope: $scope,
            buttons: {
                No: {
                    text: 'Cancelar',
                    btnClass: 'btn-default',
                    action: function(scope, button) {}
                },
                Si: {
                    text: 'Si, confirmar!',
                    btnClass: 'btn-danger',
                    action: function(scope, button) {
                        $http.delete(geturl() + 'api/cat_pills/' + _this.pill.id).then(function(xhr) {
                            $scope.getPills();
                            Notification.success({ message: 'Registro eliminado correctamente!' });
                        });
                    }
                },
            }
        });
    }
});

app.controller('ModalCatPillCtrl', function($http, $scope, $uibModalInstance, Notification) {
    $scope.save = function() {
        var data = {
            name: $scope.pill.name,
            price: $scope.pill.price,
        }
        $http.post(geturl() + 'api/cat_pills', data).then(function(xhr) {
            Notification.success({ message: 'Registro creado correctamente!' });
            $uibModalInstance.close('add');
        });
    }

    $scope.update = function() {
        var data = {
            name: $scope.pill.name,
            price: $scope.pill.price,
        }
        $http.put(geturl() + 'api/cat_pills/' + $scope.pill.id, data).then(function(xhr) {
            Notification.success({ message: 'Registro actualizado correctamente!' });
            $uibModalInstance.close('update');
        });
    }
});