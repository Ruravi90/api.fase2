var app = angular.module('App');
app.controller('CatProductsController', function($rootScope, $scope, $http, $document, $uibModal, $ngConfirm, Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;
    $scope.getProducts = function() {
        $http.get(geturl() + 'api/cat_products').then(function(xhr) {
            $scope.products = xhr.data;
        });
    }
    $scope.getProducts();
    $scope.showModal = function() {
        var parentElem = angular.element($document[0].querySelector('body'));
        $scope.modalProduct = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'modalProduct.html',
            controller: 'ModalCatProductsCtrl',
            appendTo: parentElem,
            scope: $scope
        });
        $scope.modalProduct.result.then(function(result) {
            $scope.getProducts();
        }).catch(function(error) {});
    }
    $scope.add = function() {
        $scope.IsEdit = false;
        $scope.product = {};
        $scope.showModal();
    }
    $scope.edit = function() {
        $scope.IsEdit = true;
        $http.get(geturl() + 'api/cat_products/' + this.product.id).then(function(xhr) {
            $scope.product = xhr.data;
            $scope.showModal();
        });
    }
    $scope.deleted = function(_token) {
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar el producto?',
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
                        $http.delete(geturl() + 'api/cat_products/' + _this.product.id).then(function(xhr) {
                            $scope.getProducts();
                            Notification.success({ message: 'Referencia eliminado correctamente!' });
                        });
                    }
                },
            }
        });
    }
});

app.controller('ModalCatProductsCtrl', function($http, $scope, $uibModalInstance, Notification) {
    $scope.save = function() {
        var data = {
            name: $scope.product.name,
            price: $scope.product.price,
        }

        $http.post(geturl() + 'api/cat_products', data).then(function(xhr) {
            Notification.success({ message: 'Producto creado correctamente!' });
            $scope.modalProduct.close('add');
        });
    }

    $scope.update = function() {
        var data = {
            name: $scope.product.name,
            price: $scope.product.price,
        }
        $http.put(geturl() + 'api/cat_products/' + $scope.product.id, data).then(function(xhr) {
            Notification.success({ message: 'Producto actualizado correctamente!' });
            $scope.modalProduct.close('update');
        });
    }
});