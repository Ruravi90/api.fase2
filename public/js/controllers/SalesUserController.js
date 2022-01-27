var app = angular.module('App');

app.controller('SalesController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    var $ctrl = this;
    $scope.init_roles=[];
    $scope.clients=[];
    $scope.addSale={};
    $scope.sale={};
    $scope.sale.discount = 0;
    $scope.sale.count = 1;
    $scope.saleTypes=[
        {id:"product",name:"Producto"},
        {id:"service",name:"Servicio"},
        {id:"package",name:"Paquete"},
        {id:"pill",name:"Pastillas"},
    ];
    $scope.sales=[];
    $scope.validUsername = null;

    $http.get(geturl()+'api/departments')
    .then(function(xhr){
        $scope.departments = xhr.data;
    });

    $http.get(geturl()+'api/agents')
    .then(function(xhr){
        $scope.users = xhr.data;
    });

    $http.get(geturl()+'api/clients')
    .then(function(xhr){
        $scope.clients = xhr.data;
    });

    $http.get(geturl()+'api/cat_type_sales')
    .then(function(xhr){
        $scope.type_sales = xhr.data;
    });

    $scope.Sales=function () {
        $http.get(geturl()+'api/sales/my_sales')
        .then(function(xhr){
            $scope.mySales = xhr.data;
             $scope.clearInputs();
        });
    }

    $scope.Sales();

    $scope.clearInputs=function(){
        $scope.sale={};
        $scope.sale.discount = 0;
        $scope.sale.count = 1;
        $scope.select = {};
        $scope.selectedElement={};
        $scope.sales=[];
    }

    $scope.onSelectDepartment=function(item, model){
        $scope.sale.department_id = item.id;
    }

    $scope.onSelectClient=function(item, model){
       $scope.sale.client_id = item.id; 
    }

    $scope.onSelectResponsible=function(item, model){
       $scope.sale.responsible_id = item.id; 
    }

    $scope.onSelecteTypeSale=function(item, model){
        $scope.sale.type_sale_id = item.id;
    }

    $scope.onSelectSaleType=function(item, model){
        var url = "";
        $scope.select.Element=null;
        switch (item.id) {
            case "product":
                url = geturl()+'api/cat_products';
                break;
            case "service":
                url = geturl()+'api/cat_services';
                break;
            case "package":
                url = geturl()+'api/cat_packages';
                break;
            case "pill":
                url = geturl()+'api/cat_pills';
                break;
        }

        $http.get(url)
        .then(function(xhr){
            $scope.elements = xhr.data;
        });
    }

    $scope.onSelectElement=function(item, model){
        $scope.sale.count =1;
         $scope.selectedElement = $scope.elements[$scope.elements.findIndex(e=> e.id === item.id)];
         $scope.onCalculateTotal();

         switch ($scope.select.TypeElement.id) {
            case "product":
                 $scope.sale.product_id = item.id;
                 $scope.sale.cat_product = {};
                 $scope.sale.cat_product.name = item.name;
                break;
            case "service":
                 $scope.sale.service_id = item.id;
                 $scope.sale.cat_service = {};
                 $scope.sale.cat_service.name = item.name;
                break;
            case "package":
                 $scope.sale.package_id = item.id;
                 $scope.sale.cat_package = {};
                 $scope.sale.cat_package.name = item.name;
                break;
            case "pill":
                 $scope.sale.pill_id = item.id;
                 $scope.sale.cat_pill = {};
                 $scope.sale.cat_pill.name = item.name;
                break;
        }
    }

    $scope.onCalculateTotal=function(){
        var total = $scope.selectedElement.price;
        if($scope.select.TypeElement.id == "pill" || $scope.select.TypeElement.id == "product"){
            total = $scope.selectedElement.price * $scope.sale.count;
        }
        $scope.selectedElement.subtotal = total;
        if($scope.sale.discount)
            $scope.selectedElement.discount = (($scope.sale.discount * total) / 100);
        else
            $scope.selectedElement.discount = 0;
        $scope.selectedElement.total = total - $scope.selectedElement.discount; 

        $scope.sale.price = $scope.selectedElement.price;
        $scope.sale.subtotal =  $scope.selectedElement.subtotal;
        $scope.sale.total =  $scope.selectedElement.total;
    }

    $scope.onChangeAmount=function(){
        if(this.sale.amount > $scope.selectedElement.total)
            this.sale.amount = $scope.selectedElement.total;
    }

    $scope.addSale = function(){
        $scope.sales.push(angular.copy($scope.sale));
        $scope.sale={};
        $scope.sale.discount = 0;
        $scope.sale.count = 1;
        $scope.select = {};
        $scope.selectedElement={};
    }


    $scope.deleteSale =function(index){
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
                         $scope.sales.splice(_this.$index,1);
                         $scope.$apply();
                    }
                },
            }
        });
    }


    $scope.onSave = function(){
        $http.post(geturl()+'api/sales',{sales:$scope.sales})  
        .then(function(xhr){
            $scope.Sales();
            Notification.success({message:'Venta generada correctamente!'});
        });
    }

    $scope.formatDate = function(date){
      var dateOut = new Date(date);
      return dateOut;
    };

    $scope.getElement = function(sale){
      if(sale.package_id)
        return sale.cat_package.name;
      else if(sale.pill_id)
        return sale.cat_pill.name;
      else if(sale.service_id)
        return sale.cat_service.name;
      else if(sale.product_id)
        return sale.cat_product.name;
    };

    $scope.getBoxCut  = function () {
         $http.get(geturl()+'api/sales/box_cut')
        .then(function(xhr){});
    }

    $scope.getPrintTicket = function () {
         $http.get(geturl()+'api/sales/print_ticket/'+this.sale.id)
        .then(function(xhr){});
    }
});
