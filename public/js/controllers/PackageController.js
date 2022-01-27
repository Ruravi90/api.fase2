var app = angular.module('App');

app.controller('PackageController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;
    $scope.filters={};
    $scope.filters.isCompleted = 0;

    $scope.getPackages=function(){
        $http.post(geturl()+'api/packages/is_completed',$scope.filters)
        .then(function(xhr){
            $scope.packages = xhr.data;
        });
    }

    $scope.getPackages(); 

    $scope.openModalTracker = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalTrackers = $uibModal.open({
            animation: true,
            templateUrl: 'modalTrackers.html',
            controller:'ModalTrackersCtrl', 
            scope: $scope
        })

        $scope.modalTrackers.result.then(function (result) {
            $scope.getPackages(); 
        }, function () {
        });
    }

    $scope.addTracker = function(){
        $scope.IsEdit =false;
        $scope.disabledTracker = (this.package.type.session_count <= this.package.tracking.length);
        $scope.tracker = {};
        $scope.package = this.package;
        $scope.tracker.is_taken = true;
        $scope.tracker.package_id = this.package.id;
        $scope.openModalTracker();
    }

    $scope.openModalPayments = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalPayments = $uibModal.open({
            animation: true,
            templateUrl: 'modalPayments.html',
            controller:'ModalPaymentsCtrl', 
            scope: $scope
        })

        $scope.modalPayments.result.then(function (result) {
            $scope.getPackages(); 
        }, function () {
        });
    }

    $scope.addPayment = function(){
        $scope.IsEdit =false;
        $scope.sale =  this.package.sale;
        $scope.payment ={};
        $scope.payment.is_paid = this.package.sale.is_paid;
        $scope.payment.sale_id = this.package.sale_id;

        $scope.openModalPayments();
    }
    

    $scope.getDateFinish = function(){
       var date = new Date(this.package.created_at);
       date.setDate(date.getDate() + 56);
       return date
    }

    $scope.formatDate = function(date){
      var dateOut = new Date(date);
      return dateOut;
    };
});

app.controller('ModalTrackersCtrl', function ($http,$scope,$uibModalInstance,Notification,blockUI) {

    $http.get(geturl()+'api/agents')  
    .then(function(xhr){
        $scope.users = xhr.data;
    });

    $http.get(geturl()+'api/packages_tracking/for_package/' + $scope.package.id)  
    .then(function(xhr){
        $scope.trackers = xhr.data;
    });

    $scope.save = function(){
        if($scope.disabledTracker){
            Notification.warning({message:'Sesiones completadas!'});
            return false;
        }

        blockUI.start('Guardando...');
        $scope.tracker.scheduled_date = moment($scope.tracker.scheduled_date).format('Y-MM-D h:mm:ss');

        $http.post(geturl()+'api/packages_tracking',$scope.tracker)  
        .then(function(xhr){
            blockUI.stop();
            Notification.success({message:'Sesion creado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

    $scope.isOpenCalendar = false;

    $scope.openCalendar = function(e) {
        e.preventDefault();
        e.stopPropagation();
        if(!$scope.disabledTracker)
            $scope.isOpenCalendar = true;
    };

    $scope.update = function(){ 
        var data = {
            package_id:$scope.package.id,
            is_taken:$scope.tracker.is_taken,
            description:$scope.tracker.description,
            scheduled_date:$scope.tracker.scheduled_date
        }

        $http.put(geturl()+'api/packages_tracking/'+$scope.tracker.id,data)
        .then(function(xhr){
            Notification.success({message:'Sesion actualizado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }
});

app.controller('ModalPaymentsCtrl', function ($http,$scope,$uibModalInstance,Notification,blockUI) {
    $scope.subTotal=0;
    $scope.balance=0;
    $http.get(geturl()+'api/payments/for_sale/' + $scope.payment.sale_id)  
    .then(function(xhr){
        $scope.payments = xhr.data;
        $scope.payments.forEach(function(e){
            $scope.subTotal= $scope.subTotal + e.amount;
        });

        $scope.balance = $scope.sale.price - $scope.subTotal;
    });

    $scope.save = function(){
        blockUI.start('Guardando...');

        $http.post(geturl()+'api/payments',$scope.payment)  
        .then(function(xhr){
            blockUI.stop();
            Notification.success({message:'Abono registrado correctamente!'});
            $uibModalInstance.close('ok');
        });
    }

});