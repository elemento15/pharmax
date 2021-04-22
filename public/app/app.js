var app = angular.module('mainApp', ['ngRoute', 'ui.bootstrap', 'ui.select', 'toastr', 'cp.ngConfirm']);

app.config(function ($routeProvider, $provide, toastrConfig) {
	angular.extend(toastrConfig, {
		autoDismiss: false,
		containerId: 'toast-container',
		maxOpened: 0,    
		newestOnTop: true,
		positionClass: 'toast-bottom-right',
		preventDuplicates: false,
		preventOpenDuplicates: false,
		target: 'body'
	});
	
	$routeProvider
		.when('/',
			{
				controller: 'HomeController',
				templateUrl: '/partials/home.html'
			})

		.when('/customers',{
				controller: 'CustomersController',
				templateUrl: '/partials/customers/index.html'
			})
		.when('/customers-new',
			{
				controller: 'CustomersController',
				templateUrl: '/partials/customers/edit.html'
			})
		.when('/customers-edit/:id',
			{
				controller: 'CustomersController',
				templateUrl: '/partials/customers/edit.html'
			})

		.when('/vendors',{
				controller: 'VendorsController',
				templateUrl: '/partials/vendors/index.html'
			})
		.when('/vendors-new',
			{
				controller: 'VendorsController',
				templateUrl: '/partials/vendors/edit.html'
			})
		.when('/vendors-edit/:id',
			{
				controller: 'VendorsController',
				templateUrl: '/partials/vendors/edit.html'
			})
		.when('/vendors-prices/:id',
			{
				controller: 'VendorsPricesController',
				templateUrl: '/partials/vendors/prices.html'
			})

		.when('/products',{
				controller: 'ProductsController',
				templateUrl: '/partials/products/index.html'
			})
		.when('/products-new',
			{
				controller: 'ProductsController',
				templateUrl: '/partials/products/edit.html'
			})
		.when('/products-edit/:id',
			{
				controller: 'ProductsController',
				templateUrl: '/partials/products/edit.html'
			})

		.when('/purchase-orders',{
				controller: 'PurchaseOrdersController',
				templateUrl: '/partials/purchase_orders/index.html'
			})
		.when('/purchase-orders-new',
			{
				controller: 'PurchaseOrdersController',
				templateUrl: '/partials/purchase_orders/edit.html'
			})
		.when('/purchase-orders-edit/:id',
			{
				controller: 'PurchaseOrdersController',
				templateUrl: '/partials/purchase_orders/edit.html'
			})

		.when('/products-history',
			{
				controller: 'ProductsHistoryController',
				templateUrl: '/partials/products/history.html'
			})

		.when('/products-compare',
			{
				controller: 'ProductsCompareController',
				templateUrl: '/partials/products/compare.html'
			})

		.when('/cotizations',{
				controller: 'CotizationsController',
				templateUrl: '/partials/cotizations/index.html'
			})
		.when('/cotizations-new',
			{
				controller: 'CotizationsController',
				templateUrl: '/partials/cotizations/edit.html'
			})
		.when('/cotizations-edit/:id',
			{
				controller: 'CotizationsController',
				templateUrl: '/partials/cotizations/edit.html'
			})

		.otherwise({ redirectTo: '/' });

	// regular expression definitions
	app.regexpRFC = /^([A-Z,Ñ,&]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[A-Z|\d]{3})$/;
	app.regexpEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
});

app.directive('stringToNumber', function() {
	return {
		require: 'ngModel',
		link: function (scope, element, attrs, ngModel) {
			ngModel.$parsers.push(function(value) {
				return '' + value;
			});
			ngModel.$formatters.push(function(value) {
				return  parseFloat(value, 10);
			});
		}
	}
});