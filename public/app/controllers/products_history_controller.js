app.controller('ProductsHistoryController', function ($scope, $http, $route, $location, $timeout, $ngConfirm, ProductService, VendorService, $uibModal, toastr) {

	$scope.data = {
		vendor: null
	};
	
	$scope.product = {
		id: 0,
		code: '',
		description: ''
	};

	$scope.vendorsList = [];

	$scope.keyPressProduct = function (evt) {
		if (evt.keyCode == 13) {
			evt.preventDefault();
			$(evt.currentTarget).blur();
		}
	}

	$scope.searchProduct = function () {
		var description = $scope.product.description;

		ProductService.search_description({
				description: description
			}).success(function (response) {
				if (response.success) {
					if (response.product) {
						$scope.selectProduct(response.product);
					} else {
						$scope.openSearchProduct(description);
					}
				} else {
					toastr.warning(response.msg);
					$scope.clearProduct();
				}
			}).error(function (response) {
				toastr.error(response.msg || 'Error en el servidor');
			});
	}

	$scope.openSearchProduct = function (search) {
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
				$scope.selectProduct(product);
			}
		});
	}

	$scope.selectProduct = function (product) {
		$scope.product = {
			id: product.id,
			code: product.code,
			description: product.description
		};
	}

	$scope.clearProduct = function () {
		$scope.product = {
			id: 0,
			code: '',
			description: ''
		};
	}

	$scope.getVendors = function () {
		VendorService.read({
			filters: [{ field: 'active', value: 1 }]
		}).success(function (response) {
			$scope.vendorsList = response;
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.printReport = function () {
		var vendor, product;

		if (!$scope.data.vendor) {
			toastr.warning('Seleccione proveedor');
			return false;
		}

		if (! $scope.product.id) {
			toastr.warning('Seleccione producto')
			return false;
		}

		product = $scope.product.id;
		vendor = $scope.data.vendor.id;
		window.open('products/'+ product +'/rpt_history/'+ vendor);
	}

	$scope.$on('$viewContentLoaded', function (view) {
		$scope.getVendors();
	});

});