var app = angular.module('App');

app.controller('CatPackagesController', function($rootScope, $scope, $http, $document, $uibModal, $ngConfirm, Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;
    $scope.getPackages = function() {
        $http.get(geturl() + 'api/cat_packages').then(function(xhr) {
            $scope.packages = xhr.data;
        });
    }

    /**/
    $scope.getPackages();
    $scope.showModal = function() {
        var parentElem = angular.element($document[0].querySelector('body'));
        $scope.modalPackage = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'modalPackage.html',
            controller: 'ModalCatPackagesCtrl',
            appendTo: parentElem,
            scope: $scope
        });

        $scope.modalPackage.result.then(function(result) {
            $scope.getPackages();
        }).catch(function(error) {});

    }

    $scope.add = function() {
        $scope.IsEdit = false;
        $scope.package = {};
        $scope.showModal();
    }

    $scope.edit = function() {
        $scope.IsEdit = true;
        $http.get(geturl() + 'api/cat_packages/' + this.package.id).then(function(xhr) {
            $scope.package = xhr.data;
            $scope.showModal();
        });
    }

    $scope.deleted = function(_token) {
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar la Paquete?',
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
                        $http.delete(geturl() + 'api/cat_packages/' + _this.package.id).then(function(xhr) {
                            $scope.getPackages();
                            Notification.success({
                                message: 'Paquete eliminado correctamente!'
                            });
                        });
                    }
                },
            }
        });
    }
});

app.controller('ModalCatPackagesCtrl', function($http, $scope, $uibModalInstance, Notification) {
    $scope.optElements = 'na';
    $scope.addElement = {};
    $scope.save = function() {
        $http.post(geturl() + 'api/cat_packages', $scope.package).then(function(xhr) {
            Notification.success({
                message: 'Paquete creada correctamente!'
            });
            $uibModalInstance.close('add');
        }).error(function() {
            Notification.error({
                message: 'Error al crear paquete!'
            });
            console.log("AJAX failed!");
        });
    }

    $scope.changeOptions = function() {
        $scope.select = {};
        $scope.getElements();
    }

    $scope.getElements = function() {
        var url = geturl() + 'api/';

        if ($scope.optElements == 'pill')
            url += 'cat_pills';
        else if ($scope.optElements == 'products')
            url += 'cat_products';
        else
            return false;

        $http.get(url).then(function(xhr) {
            $scope.elements = xhr.data;
        });
    }

    $scope.getElements();

    $scope.onSelectElement = function(i, m) {
        delete $scope.addElement.pill_id;
        delete $scope.addElement.product_id;

        if ($scope.optElements == 'pill')
            $scope.addElement.id = i.id;
        else
            $scope.addElement.id = i.id;

        $scope.addElement.name = i.name;
    }

    $scope.addElements = function() {
        if ($scope.package.complements == undefined)
            $scope.package.complements = [];

        if ($scope.optElements == 'pill')
            $scope.package.complements.push({
                pill_id: $scope.addElement.id,
                cat_pill: $scope.addElement,
                count: $scope.addElement.count
            });
        else
            $scope.package.complements.push({
                product_id: $scope.addElement.id,
                cat_product: $scope.addElement,
                count: $scope.addElement.count
            });

        $scope.addElement = {};
        $scope.select = {};
    }

    $scope.deleteElements = function(index) {
        $scope.package.complements.splice(index, 1);
    }

    $scope.update = function() {
        $http.put(geturl() + 'api/cat_packages/' + $scope.package.id, $scope.package).then(function(xhr) {
            Notification.success({
                message: 'Paquete actualizado correctamente!'
            });
            $uibModalInstance.close('update');
        });
    }
});