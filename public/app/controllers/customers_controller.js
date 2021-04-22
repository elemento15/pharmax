app.controller('CustomersController', function ($scope, $http, $route, $location, $ngConfirm, toastr, $uibModal,
	                                            CustomerService, CotizationService, PaymentTypeService, PaymentService) {
	this.index = '/customers';
	this.title = {
		new:  'Nuevo Cliente',
		edit: 'Editar Cliente'
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
		address: '',
		active: 1,
		comments: '',
		balance: 0
	};

	$scope.filters = {
		active: ''
	}

	$scope.paymentTypesList = [];

	$scope.cotizationsFilters = {
		status: 'N'
	};
	$scope.cotizationList = [];
	$scope.cotizationPagination = {
		page: 1,
		total: 1,
		limit: 4
	};
	$scope.pageCotizationInfo = '1/1';
	$scope.selCotizationIndex = 0;

	$scope.getCotizations = function (customer_id) {
		var id = customer_id || $scope.data.id;
		var status = $scope.cotizationsFilters.status;
		var filters = [{ field: 'customer_id', value: id }];
		var pagination = $scope.cotizationPagination;

		if (status) {
			filters.push({ field: 'status', value: status });
		}

		CotizationService.read({
			page: pagination.page,
			filters: filters,
			limit: pagination.limit
		}).success(function (response) {
			$scope.cotizationList = response.data;
			$scope.setCotizationPagination(response);
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

	$scope.selectCotization = function (key) {
		$scope.selCotizationIndex = key;
	}

	$scope.openPaymentModal = function (cotization) {
		var modal = $uibModal.open({
			ariaLabelledBy: 'modal-title',
			ariaDescribedBy: 'modal-body',
			templateUrl: '/partials/templates/modalPayment.html',
			controller: 'ModalPayment',
			controllerAs: '$ctrl',
			resolve: {
				items: function () {
					return {
						model: cotization,
						modelType: 'COTIZATION',
						paymentTypesList: $scope.paymentTypesList
					};
				}
			}
		});

		modal.result.then(function () {
			$scope.getCotizations($scope.data.id);
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
							$scope.getCotizations();
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

	$scope.setCotizationPagination = function (data) {
		var pagination = $scope.cotizationPagination;
		pagination.total = data.last_page;
		$scope.pageCotizationInfo = (pagination.page) +'/'+ pagination.total;
	}

	$scope.paginateCotization = function (type, force) {
		var data = $scope.cotizationPagination;
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
			$scope.getCotizations(); // read only if page has changed
		}
	}

	$scope.refreshBalance = function () {
		CustomerService.get({
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
			$scope.getCotizations(id);
			$scope.getPaymentTypes();
		}
	});

	BaseController.call(this, $scope, $route, $location, $ngConfirm, CustomerService, toastr);
});