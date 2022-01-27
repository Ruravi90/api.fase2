var app = angular.module('App');
app.controller('PurchaseController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification,blockUI) {

    $scope.getPurchases=function(){
        $http.post(geturl()+'api/purchases_pending',{is_paid:false})
        .then(function(xhr){
            $scope.purchases = xhr.data;
        });
    }

    $scope.getPurchases(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalPurchases = $uibModal.open({
            animation: true,
            templateUrl: 'modalPurchases.html',
            controller:'ModalPurchasesCtrl', 
            scope: $scope
        })

        $scope.modalPurchases.result.then(function (result) {
            $scope.getPurchases(); 
        }, function () {
        });
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit =true;
        $scope.purchase = this.purchase;
        $scope.showModal();
    }

    // add purchase
    // 
    $scope.select= {};
    $scope._purchases = [];
    $scope.purchase = {};
    $scope.purchase.count = 0;

    $scope.getCatlog = function(){
        $http.get(geturl()+'api/departments')  
        .then(function(xhr){
            $scope.departments = xhr.data;
        });

        $http.get(geturl()+'api/providers')  
        .then(function(xhr){
            $scope.providers = xhr.data;
        });

        $http.get(geturl()+'api/cat_concepts')  
        .then(function(xhr){
            $scope.concepts = xhr.data;
            $scope.concepts.push({id:-1,name:'Otro'});
        });

        $http.get(geturl()+'api/cat_pills')  
        .then(function(xhr){
            $scope.pills = xhr.data;
            $scope.pills.push({id:-1,name:'Otro'});
        });

        $http.get(geturl()+'api/cat_products')  
        .then(function(xhr){
            $scope.products = xhr.data;
            $scope.products.push({id:-1,name:'Otro'});
        });

        $http.get(geturl()+'api/cat_expenses')  
        .then(function(xhr){
            $scope.expenses = xhr.data;
            $scope.expenses.push({id:-1,name:'Otro'});
        });
    }

    $scope.getCatlog();


    $scope.onSelectDepartment=function(item, model){
        $scope.purchase.department_id = item.id;
        $scope.purchase.department = {};
        $scope.purchase.department.name = item.name;
    }
    $scope.onSelectProvider=function(item, model){
        $scope.purchase.provider_id = item.id;
        $scope.purchase.cat_provider = {};
        $scope.purchase.cat_provider.name = item.name;
    }

    $scope.onSelectExpense=function(item, model){
        $scope.purchase.expense_id = item.id;
        $scope.purchase.cat_expence = {};
        $scope.purchase.cat_expence.name = item.name;

        $scope.purchase.pill_id = null;
        $scope.purchase.product_id = null;
    }

    $scope.onSelectConcept=function(item, model){
        $scope.purchase.concept_id = item.id;
        $scope.purchase.cat_concept = {};
        $scope.purchase.cat_concept.name = item.name;
    }

    $scope.formatDate = function(date){
      var dateOut = new Date(date);
      return dateOut;
    };

    
    $scope.addPurchare = function(){
        $scope._purchases.push(angular.copy($scope.purchase));
        $scope.purchase = {};
        $scope.purchase.count = 0;
        $scope.select = {};
    }

    $scope.save = function(){
        blockUI.start('Guardando...');
        $http.post(geturl()+'api/purchases',{purchases:$scope._purchases})  
        .then(function(xhr){
            blockUI.stop();
            $scope._purchases=[];
            $scope.getPurchases(); 
            $scope.getCatlog();
            Notification.success({message:'Compra guardada correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.delete =function(){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar el concepto?',
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
                         $scope._purchases.splice(_this.$index,1);
                         $scope.$apply();
                    }
                },
            }
        });
    }

});