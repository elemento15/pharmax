app.controller('PurchaseOrdersController', function ($scope, $http, $route, $location, $timeout, $ngConfirm, $uibModal, PurchaseOrderService, ProductService, VendorService, toastr) {
	this.index = '/purchase-orders';
	this.title = {
		new:  'Nueva Orden de Compra',
		edit: 'Orden de Compra: '
	}

	this.validation = function () {
		var data = $scope.data;
		var invalid = false;

		if (! data.vendor) {
			invalid = toastr.warning('Proveedor requerido', 'Validaciones');
		} else {
			data.vendor_id = data.vendor.id; // select2
		}

		return (invalid) ? false : data;
	}

	// model data
	$scope.data = {
		id:        0,
		vendor_id: '',
		status: 'N',
		order_date: '',
		subtotal: 0,
		iva_amount: 0,
		total: 0,
		active: 1,
		comments: '',
		purchase_order_details: [],
		vendor: null
	};

	$scope.filters = {
		status: ''
	}

	$scope.product = {
		id: 0,
		code: '',
		description: '',
		quantity: '',
		price: '',
		subtotal: 0,
		iva: 0
	};

	$scope.input_quantity = 0;
	$scope.input_price = 0;

	$scope.vendorsList = [];

	$scope.getVendors = function () {
		VendorService.read({
			filters: [{ field: 'active', value: 1 }]
		}).success(function (response) {
			$scope.vendorsList = response;
		}).error(function (response) {
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
					$scope.getProductPrice(response.product);
					$scope.focusQuantity();
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
						$scope.getProductPrice(response.product);
						$scope.focusQuantity();
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
			quantity: 1,
			price: 0,
			subtotal: 0,
			iva: product.iva
		};
	}

	$scope.setFocus = function (evt, opt) {
		if (evt.keyCode == 13) {
			evt.preventDefault();

			switch (opt) {
				case 'price' : $('input[ng-model="product.price"]').focus().select();
				               break;
				case 'add'   : $('#btnAddProduct').focus();
				               break;
			}
		}
	}

	$scope.addProduct = function () {
		var product = $scope.product;
		var iva_amount = 0;
		var total = 0;

		if (! product.id) {
			toastr.warning('Seleccione un producto válido');
			$('input[ng-model="product.code"]').focus().select();
			return false;
		}

		iva_amount = (product.iva / 100) * product.subtotal;
		total = product.subtotal + iva_amount;
		
		$scope.data.purchase_order_details.push({
			product_id: product.id,
			quantity: product.quantity,
			price: product.price,
			subtotal: product.subtotal,
			iva: product.iva,
			iva_amount: iva_amount,
			total: total,
			product: {
				id: product.id,
				code: product.code,
				description: product.description
			}
		});

		$scope.clearProduct();
		$('input[ng-model="product.code"]').focus().select();

		$scope.calculateTotal();
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
				$scope.getProductPrice(product);
				$scope.focusQuantity();
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
			subtotal: 0,
			iva: 0
		};
	}

	$scope.deleteProduct = function (product, key) {
		$scope.data.purchase_order_details[key]._deleted = true;
		$scope.calculateTotal();
		$scope.focusCode();
	}

	$scope.calculateDetailTotals = function () {
		var product = $scope.product;
		var subtotal = product.quantity * product.price;
		var iva_amount = subtotal * product.iva;

		$scope.product.subtotal = subtotal;
	}

	$scope.calculateTotal = function () {
		var subtotal = 0;
		var iva_amount = 0;
		var total = 0;

		$scope.data.purchase_order_details.forEach(function (item) {
			if (! item._deleted) {
				subtotal += item.subtotal;
				iva_amount += item.iva_amount;
				total += item.total;
			}
		});

		$scope.data.subtotal = subtotal;
		$scope.data.iva_amount = iva_amount;
		$scope.data.total = total;
	}

	$scope.focusQuantity = function () {
		$timeout(function () {
			$('input[ng-model="product.quantity"]').focus().select();
	    }, 100);
	}

	$scope.focusCode = function () {
		$timeout(function () {
			$('input[ng-model="product.code"]').focus().select();
	    }, 100);
	}

	$scope.focusDescription = function () {
		$timeout(function () {
			$('input[ng-model="product.description"]').focus().select();
	    }, 100);
	}

	$scope.getProductPrice = function (product) {
		// if no vendor selected, set price = 0
		if (! $scope.data.vendor) {
			$scope.product.price = 0;
			return false;
		}

		ProductService.get_price({
			id: product.id,
			vendor: $scope.data.vendor.id
		}).success(function (response) {
			$scope.product.price = response.price;
			$scope.calculateDetailTotals();
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.editDetail = function (detail, type) {
		if (type == 'quantity') {
			$scope.input_quantity = parseFloat(detail.quantity);
			detail.editing_quantity = true;
			$timeout(function () {
				$('input[ng-model="input_quantity"]').focus().select();
		    }, 50);
		}

		if (type == 'price') {
			$scope.input_price = parseFloat(detail.price);
			detail.editing_price = true;
			$timeout(function () {
				$('input[ng-model="input_price"]').focus().select();
		    }, 50);
		}
	}

	$scope.endEditDetail = function (detail, type, evt) {
		var value = parseFloat(evt.target.value);
		if (! value) {
			toastr.warning('Capture un número válido', 'Validaciones');
			$scope.clearEditingDetails('all');
			return false;
		}
		
		if (type == 'quantity') {
			detail.quantity = value;
		}

		if (type == 'price') {
			detail.price = value;
		}

		$scope.clearEditingDetails(type);
		detail.subtotal = detail.quantity * detail.price;
		detail.iva_amount = (detail.iva / 100) * detail.subtotal;
		detail.total = detail.subtotal + detail.iva_amount;
		$scope.calculateTotal();
	}

	$scope.keyPressDetail = function (evt, type) {
		var key = evt.keyCode;

		if (key == 27) {
			$scope.clearEditingDetails(type);
		}

		if (key == 13) {
			evt.target.blur();
		}
	}

	$scope.clearEditingDetails = function (type) {
		$scope.data.purchase_order_details.forEach(function (item) {
			if (type == 'quantity' || type == 'all') {
				item.editing_quantity = false;
			}

			if (type == 'price' || type == 'all') {
				item.editing_price = false;
			}
		});
	}

	$scope.cancel = function () {
		var id = $scope.data.id;

		$ngConfirm({
			title: 'Cancelar',
			content: '¿Desea cancelar el registro actual?',
			type: 'red',
			buttons: {
				ok: {
					text: 'Aceptar',
					btnClass: 'btn-red',
					action: function () {
						PurchaseOrderService.cancel({
							id: id
						}).success(function (response) {
							toastr.success('Registro Cancelado');
							$location.path('/purchase-orders');
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

	$scope.getVendors();

	BaseController.call(this, $scope, $route, $location, $ngConfirm, PurchaseOrderService, toastr);
});