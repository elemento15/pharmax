app.controller('ModalProductsSearch', function ($uibModalInstance, items, ProductService) {
  var $ctrl = this;
  
  var filters = [{ field: 'active', value: 1 }];
  var pagination = {
    page: 1,
    total: 1,
    limit: 5
  };

  $ctrl.products = [];
  $ctrl.search = items.search;
  $ctrl.pageInfo = '1/1';
  $ctrl.selectedId = null;
  $ctrl.selection = null;

  $ctrl.read = function () {
    ProductService.read({
      page: pagination.page,
      filters: filters,
      search: $ctrl.search
    }).success(function (response) {
      $ctrl.products = response.data;
      $ctrl.setPagination(response, pagination);
    }).error(function (response) {
      toastr.error(response.msg || 'Error en el servidor');
    });
  }

  $ctrl.ok = function () {
    $uibModalInstance.close($ctrl.selection);
  };

  $ctrl.cancel = function () {
    $uibModalInstance.dismiss('cancel');
  };

  $ctrl.searchData = function () {
    $ctrl.paginate('first', true);
  }

  $ctrl.setPagination = function (data, pagination) {
    pagination.total = data.last_page;
    $ctrl.pageInfo = (pagination.page) +'/'+ pagination.total;
  };

  $ctrl.paginate = function (type, force) {
    var data = pagination;
    var page = data.current_page;

    switch (type) {
      case 'first' :
        data.page = 1;
        break;
      case 'previous' :
        if (data.page > 1) {
          data.page--;
        }
        break;
      case 'next' :
        if (data.page < data.total) {
          data.page++;
        }
        break;
      case 'last' :
        data.page = data.total;
        break;
    }

    if (page != data.page || force) {
      $ctrl.read(); // read only if page has changed
    }
  };

  $ctrl.setSelection = function (record) {
    $ctrl.selectedId = record.id;
    $ctrl.selection = record;
  }

  $ctrl.read();

});