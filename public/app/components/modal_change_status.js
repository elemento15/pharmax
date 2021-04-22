app.controller('ModalChangeStatus', function ($uibModalInstance, items, StatusService) {
  var $ctrl = this;

  $ctrl.statusList = items.statusList;

  $ctrl.status = null;

  $ctrl.ok = function () {
    $uibModalInstance.close($ctrl.status);
  };

  $ctrl.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };

  $ctrl.setStatus = function (status) {
    $ctrl.status = status;
  }

});