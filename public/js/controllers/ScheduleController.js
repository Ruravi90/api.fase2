var app = angular.module('App');
app.controller('ScheduleController',function($rootScope,$scope, $http,$document,$uibModal,$ngConfirm,Notification) {
    $scope.pageSize = 10;
    $scope.currentPage = 1;

    $('#fullcalendar').fullCalendar({
        dayClick: function() {
            alert('a day has been clicked!');
        }
    });

    $scope.getSchedules=function(){
        $http.get(geturl()+'api/schedules')
        .then(function(xhr){
            $scope.schedules = xhr.data;
        });
    }

    $scope.getSchedules(); 

    $scope.showModal = function(){
        var parentElem =  angular.element($document[0].querySelector('body'));
        $scope.modalSchedule = $uibModal.open({
            animation: true,
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'modalSchedule.html',
            controller: 'ModalSchedulCtrl',
            appendTo: parentElem,
            scope: $scope
        });

        $scope.modalSchedule.result
        .then(function(result){
            $scope.getSchedules();
        }).catch(function(result){

        }); 
    }

    $scope.add = function(){
        $scope.IsEdit =false;
        $scope.schedule ={};
        $scope.showModal();
    }

    $scope.edit = function(){
        $scope.IsEdit = true;
        $http.get(geturl()+'api/schedules/'+this.client.id)
        .then(function(xhr){
            $scope.schedule = xhr.data;
            $scope.showModal();
        }); 
    }

    $scope.deleted= function(_token){
        var _this = this;
        $ngConfirm({
            title: 'Confirmar!',
            content: 'Seguro de eliminar la cita?',
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
                        $http.delete(geturl()+'api/schedules/'+_this.client.id) 
                        .then(function(xhr){
                            $scope.getClients();
                            Notification.success({message:'Cita eliminada correctamente!'});
                        });
                    }
                },
            }
        });
    }
});


app.controller('ModalSchedulCtrl', function ($http,$scope,$uibModalInstance,Notification) {

    $scope.save = function(form){
         if(form.$invalid){
            Notification.error({message:'Campos por llenar!'});
            return false;
        }
        var data = {
            title:$scope.schedule.title,
            description:$scope.schedule.description,
            start:$scope.schedule.start,
            end:$scope.schedule.end,
            color:$scope.schedule.color,
            allDay:$scope.schedule.allDay,
            client_id:$scope.schedule.client_id,
        }

        $http.post(geturl()+'api/schedules',data) 
        .then(function(xhr){
            Notification.success({message:'Cita creada correctamente!'});
            $uibModalInstance.close('add');
        });
    }

    $scope.update = function(form){

        if(form.$invalid){
            Notification.error({message:'Campos por llenar!'});
            return false;
        }

        var data = {
            title:$scope.schedule.title,
            description:$scope.schedule.description,
            start:$scope.schedule.start,
            end:$scope.schedule.end,
            color:$scope.schedule.color,
            allDay:$scope.schedule.allDay,
            client_id:$scope.schedule.client_id,
        }

        $http.put(geturl()+'api/schedules/'+$scope.client.id,data)
        .then(function(xhr){
            Notification.success({message:'Cita actualizada correctamente!'});
            $uibModalInstance.close('update');
        });
    }
});