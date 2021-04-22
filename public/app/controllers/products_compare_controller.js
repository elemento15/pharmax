app.controller('ProductsCompareController', function ($scope, $http, $route, $location, $ngConfirm, ProductService, toastr) {

	var pagination = {
		page: 1,
		total: 1,
		limit: 5
	};

	$scope.list = [];
	$scope.keySelected = null;
	$scope.selected = false;
	$scope.optionSelected = null;

	$scope.search = '';
	$scope.orderBy = 'C';
	$scope.pageInfo = '1/1';

	$scope.read = function () {
		ProductService.rpt_compare({
			page: pagination.page,
			search: $scope.search,
			order: $scope.orderBy
		}).success(function (response) {
			$scope.list = response.data;
			$scope.selected = false;
			$scope.keySelected = null;
			$scope.setPagination(response, pagination);
		}).error(function (response) {
			toastr.error(response.msg || 'Error en el servidor');
		});
	}

	$scope.searchData = function () {
		$scope.paginate('first', true);
	}

	$scope.productSelected = function (record, key) {
		$scope.selected = record;
		$scope.keySelected = key;
		$scope.optionSelected = null;
	}

	$scope.showVendorInfo = function (key) {
		$scope.optionSelected = key;
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
		$scope.read();
	});

});