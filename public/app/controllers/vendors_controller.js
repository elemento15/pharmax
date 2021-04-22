app.controller('VendorsController', function ($scope, $http, $route, $location, $ngConfirm, toastr, $uibModal, 
	                                          VendorService, PurchaseOrderService, PaymentTypeService, PaymentService) {
	this.index = '/vendors';
	this.title = {
		new:  'Nuevo Proveedor',
		edit: 'Editar Proveedor'
	}

	this.validation = function () {
		var data = $scope.data;
		var invalid = false;

		if (! data.name) {
			invalid = toastr.warning('Nombre requerido', 'Validaciones');
		}

		if (data.rfc && !app.regexpRFC.test(data.rfc)) {
			invalid = toastr.warning('RFC Inválido', 'Validaciones');
		}

		if (data.email && !app.regexpEmail.test(data.email)) {
			invalid = toastr.warning('Email Inválido', 'Validaciones');
		}

		return (invalid) ? false : data;
	}

	// model data
	$scope.data = {
		id:        0,
		name: '',
		rfc: '',
		contact: '',
		phone: '',
		mobile: '',
		email: '',
		credit_conditions: '',
		address: '',
		active: 1,
		comments: '',
		balance: 0
	};

	$scope.filters = {
		active: ''
	}

	$scope.paymentTypesList = [];

	$scope.ordersFilters = {
		status: 'N'
	};
	$scope.orderList = [];
	$scope.orderPagination = {
		page: 1,
		total: 1,
		limit: 4
	};
	$scope.pageOrderInfo = '1/1';
	$scope.selOrderIndex = 0;

	$scope.getOrders = function (vendor_id) {
		var id = vendor_id || $scope.data.id;
		var status = $scope.ordersFilters.status;
		var filters = [{ field: 'vendor_id', value: id }];
		var pagination = $scope.orderPagination;

		if (status) {
			filters.push({ field: 'status', value: status });
		}

		PurchaseOrderService.read({
			page: pagination.page,
			filters: filters,
			limit: pagination.limit
		}).success(function (response) {
			$scope.orderList = response.data;
			$scope.setOrderPagination(response);
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.getPaymentTypes = function () {
		PaymentTypeService.read({
			filters: [{ field: 'active', value: 1 }]
		}).success(function (response) {
			$scope.paymentTypesList = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.selectOrder = function (key) {
		$scope.selOrderIndex = key;
	}

	$scope.openPaymentModal = function (order) {
		var modal = $uibModal.open({
			ariaLabelledBy: 'modal-title',
			ariaDescribedBy: 'modal-body',
			templateUrl: '/partials/templates/modalPayment.html',
			controller: 'ModalPayment',
			controllerAs: '$ctrl',
			resolve: {
				items: function () {
					return {
						model: order,
						modelType: 'PURCHASE',
						paymentTypesList: $scope.paymentTypesList
					};
				}
			}
		});

		modal.result.then(function () {
			$scope.getOrders($scope.data.id);
			$scope.refreshBalance();
		});
	}

	$scope.cancelPayment = function (payment) {
		$ngConfirm({
			title: 'Cancelar Abono',
			content: '¿Desea cancelar el abono seleccionado?',
			type: 'red',
			buttons: {
				ok: {
					text: 'Aceptar',
					btnClass: 'btn-red',
					action: function () {
						PaymentService.cancel({
							id: payment.id
						}).success(function (response) {
							toastr.success('Abono Cancelado');
							$scope.getOrders();
						}).error(function (response) {
							toastr.error(response.msg || 'Error en el servidor');
						});
					}
				},
				close: {
					text: 'Omitir',
					btnClass: 'btn-default'
				}
			}
		});
	}

	$scope.setOrderPagination = function (data) {
		var pagination = $scope.orderPagination;
		pagination.total = data.last_page;
		$scope.pageOrderInfo = (pagination.page) +'/'+ pagination.total;
	}

	$scope.paginateOrder = function (type, force) {
		var data = $scope.orderPagination;
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
			$scope.getOrders(); // read only if page has changed
		}
	}

	$scope.refreshBalance = function () {
		VendorService.get({
			id: $scope.data.id
		}).success(function (response) {
			$scope.data.balance = response.balance;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.$on('$viewContentLoaded', function (view) {
		var id = $route.current.params.id;
		if (id) {
			$scope.getOrders(id);
			$scope.getPaymentTypes();
		}
	});

	BaseController.call(this, $scope, $route, $location, $ngConfirm, VendorService, toastr);
});