app.controller('VendorsPricesController', function ($scope, $http, $route, $location, $timeout, $ngConfirm, $uibModal, VendorPriceService, VendorService, ProductService, toastr) {
	
	var pagination = {
		page: 1,
		total: 1,
		limit: 5
	};

	$scope.list = [];
	$scope.orderBy = 'C'; // order by code
	$scope.vendor = {};

	$scope.filters = {
		vendor_id: 0
	};

	$scope.product = {
		id: 0,
		code: '',
		description: '',
		price: ''
	};

	$scope.input_price = 0;

	$scope.search = '';
	$scope.pageInfo = '1/1';

	$scope.getVendor = function (id) {
		VendorService.get({
			id : id
		}).success(function(response) {
			$scope.vendor = response;
		}).error(function(response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.searchCode = function (evt) {
		var code;
		
		if (evt.keyCode == 13) {
			evt.preventDefault();

			code = $scope.product.code;

			if (code == '') {
				$scope.focusDescription();
				return false;
			}

			ProductService.search_code({
				code: code
			}).success(function (response) {
				if (response.success) {
					$scope.setProduct(response.product);
					$scope.focusPrice();
				} else {
					toastr.warning(response.msg);
					$scope.clearProduct();
					$('input[ng-model="product.code"]').focus().select();
				}
			}).error(function (response) {
				toastr.error(response.msg || 'Error en el servidor');
			});
		}
	}

	$scope.searchDescription = function (evt) {
		var description;

		if (evt.keyCode == 13) {
			evt.preventDefault();

			description = $scope.product.description;

			ProductService.search_description({
				description: description
			}).success(function (response) {
				if (response.success) {
					if (response.product) {
						$scope.setProduct(response.product);
						$scope.focusPrice();
					} else {
						$scope.openSearch(description);
					}
				} else {
					toastr.warning(response.msg);
					$scope.clearProduct();
					$('input[ng-model="product.code"]').focus().select();
				}
			}).error(function (response) {
				toastr.error(response.msg || 'Error en el servidor');
			});
		}
	}

	$scope.setProduct = function (product) {
		$scope.product = {
			id: product.id,
			code: product.code,
			description: product.description,
			price: 0
		};
	}

	$scope.setFocus = function (evt, opt) {
		if (evt.keyCode == 13) {
			evt.preventDefault();

			switch (opt) {
				case 'add'   : $('#btnAddProduct').focus();
				               break;
			}
		}
	}

	$scope.focusDescription = function () {
		$timeout(function () {
			$('input[ng-model="product.description"]').focus().select();
	    }, 100);
	}

	$scope.focusPrice = function () {
		$timeout(function () {
			$('input[ng-model="product.price"]').focus().select();
	    }, 100);
	}

	$scope.addProduct = function () {
		var product = $scope.product;

		if (! product.id) {
			toastr.warning('Seleccione un producto válido');
			$('input[ng-model="product.code"]').focus().select();
			return false;
		}
		
		VendorPriceService.save({
			vendor_id: $scope.vendor.id,
			product_id: product.id,
			price: product.price
		}).success(function(response) {
			toastr.success('Producto guardado');
			$scope.read();
			$scope.clearProduct();
			$('input[ng-model="product.code"]').focus().select();

		}).error(function(response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.openSearch = function (search) {
		var modal = $uibModal.open({
			ariaLabelledBy: 'modal-title',
			ariaDescribedBy: 'modal-body',
			templateUrl: '/partials/templates/modalProducts.html',
			controller: 'ModalProductsSearch',
			controllerAs: '$ctrl',
			resolve: {
				items: function () {
					return {
						search: search || ''
					};
				}
			}
		});

		modal.result.then(function (product) {
			if (product) {
				$scope.setProduct(product);
				$scope.focusPrice();
			}
		});
	}

	$scope.clearProduct = function () {
		$scope.product = {
			id: 0,
			code: '',
			description: '',
			quantity: '',
			price: '',
			total: 0
		};
	}

	$scope.read = function () {
		VendorPriceService.read({
			page: pagination.page,
			filters: $scope.mapFilters(),
			search: $scope.search,
			order: $scope.orderBy
		}).success(function (response) {
			$scope.list = response.data;
			$scope.setPagination(response, pagination);
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.deleteProduct = function (id) {
		$ngConfirm({
			title: 'Eliminar',
			content: '¿Desea eliminar el registro seleccionado?',
			type: 'red',
			buttons: {
				ok: {
					text: 'Aceptar',
					btnClass: 'btn-red',
					action: function () {
						VendorPriceService.delete({
							id: id
						}).success(function (response) {
							toastr.warning('Registro eliminado');
							$scope.paginate('first', true);
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

	$scope.editPrice = function (detail) {
		$scope.clearEditingDetails();

		$scope.input_price = detail.price;
		detail.editing = true;
		
		$timeout(function () {
			$('input[ng-model="input_price"]').focus().select();
	    }, 50);
	}

	$scope.clearEditingDetails = function () {
		$scope.list.forEach(function (item) {
			item.editing = false;
		});
	}

	$scope.endEditDetail = function (detail, evt) {
		if (detail.price != evt.target.value) {
			// update detail's price
			
			VendorPriceService.save({
				id: detail.id,
				price: evt.target.value
			}).success(function(response) {
				toastr.success('Precio guardado');
				// detail.price = response.price;
				$scope.read();
			})
			.error(function(response) {
				toastr.error(response.msg || 'Error en el servidor');
			});
		}

		$scope.clearEditingDetails();
	}

	$scope.keyPressPrice = function (evt) {
		var key = evt.keyCode;

		if (key == 27) {
			$scope.clearEditingDetails();
		}

		if (key == 13) {
			evt.target.blur();
		}
	}

	$scope.searchData = function () {
		$scope.paginate('first', true);
	}

	$scope.mapFilters = function () {
		var filters = [];

		$.map($scope.filters, function (value, index) {
			if (value) {
				filters.push({
					field: index,
					value: value
				});
			}
		});

		return filters;
	}

	$scope.setPagination = function (data, pagination) {
		pagination.total = data.last_page;
		$scope.pageInfo = (pagination.page) +'/'+ pagination.total;
	}

	$scope.paginate = function (type, force) {
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
			$scope.read(); // read only if page has changed
		}
	}

	$scope.$on('$viewContentLoaded', function (view) {
		var id = $route.current.params.id;
		if (id) {
			$scope.filters.vendor_id = id;
			$scope.getVendor(id);
			$scope.read(id);
		}
	});
	
});