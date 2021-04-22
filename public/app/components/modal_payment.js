app.controller('ModalPayment', function ($uibModalInstance, items, CotizationService, PurchaseOrderService, toastr) {
  var $ctrl = this;

  
  $ctrl.payment = {
    amount: 0,
    type_id: '',
    comments: ''
  };
  $ctrl.model = items.model;
  $ctrl.modelType = items.modelType;
  $ctrl.paymentTypesList = items.paymentTypesList;
  
  $ctrl.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };

  $ctrl.save = function () {
    
    if ($ctrl.modelType == 'COTIZATION') {
      CotizationService.savePayment({
        cotization_id: $ctrl.model.id,
        amount: $ctrl.payment.amount,
        payment_type_id: $ctrl.payment.type_id,
        comments: $ctrl.payment.comments
      }).success(function (response) {
        toastr.success('Abono creado exitosamente');
        $uibModalInstance.close();
      }).error(function (response) {
        toastr.error(response.msg || 'Error en el servidor');
      });
    }

    if ($ctrl.modelType == 'PURCHASE') {
      PurchaseOrderService.savePayment({
        purchase_id: $ctrl.model.id,
        amount: $ctrl.payment.amount,
        payment_type_id: $ctrl.payment.type_id,
        comments: $ctrl.payment.comments
      }).success(function (response) {
        toastr.success('Abono creado exitosamente');
        $uibModalInstance.close();
      }).error(function (response) {
        toastr.error(response.msg || 'Error en el servidor');
      });
    }

  }

});