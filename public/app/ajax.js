app.factory('CustomerService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('customers/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('customers/'+ data.id, data) : $http.post('customers', data);
		},
		read     : function(data) {
			return $http.get('customers?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('customers/'+ data.id);
		},
		activate : function(data) {
			return $http.post('customers/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('customers/'+ data.id +'/deactivate');
		}
	} 
}]);

app.factory('VendorService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('vendors/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('vendors/'+ data.id, data) : $http.post('vendors', data);
		},
		read     : function(data) {
			return $http.get('vendors?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('vendors/'+ data.id);
		},
		activate : function(data) {
			return $http.post('vendors/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('vendors/'+ data.id +'/deactivate');
		}
	}
}]);

app.factory('VendorPriceService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('vendor_prices/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('vendor_prices/'+ data.id, data) : $http.post('vendor_prices', data);
		},
		read     : function(data) {
			return $http.get('vendor_prices?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('vendor_prices/'+ data.id);
		}
	}
}]);

app.factory('ProductService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('products/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('products/'+ data.id, data) : $http.post('products', data);
		},
		read     : function(data) {
			return $http.get('products?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('products/'+ data.id);
		},
		activate : function(data) {
			return $http.post('products/'+ data.id +'/activate');
		},
		deactivate : function(data) {
			return $http.post('products/'+ data.id +'/deactivate');
		},
		search_code : function (code) {
			return $http.post('products/search_code', code);
		},
		search_description : function (description) {
			return $http.post('products/search_description', description);
		},
		get_price : function (data) {
			return $http.post('products/get_price', data);
		},
		rpt_compare: function (data) {
			return $http.post('products/rpt_compare', data);
		}
	}
}]);

app.factory('PurchaseOrderService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('purchase_orders/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('purchase_orders/'+ data.id, data) : $http.post('purchase_orders', data);
		},
		read     : function(data) {
			return $http.get('purchase_orders?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('purchase_orders/'+ data.id);
		},
		cancel   : function(data) {
			return $http.post('purchase_orders/'+ data.id +'/cancel');
		},
		savePayment : function(data) {
			return $http.post('purchase_orders/'+ data.purchase_id +'/save_payment', data);
		}
	}
}]);

app.factory('CotizationService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('cotizations/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('cotizations/'+ data.id, data) : $http.post('cotizations', data);
		},
		read     : function(data) {
			return $http.get('cotizations?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('cotizations/'+ data.id);
		},
		cancel   : function(data) {
			return $http.post('cotizations/'+ data.id +'/cancel');
		},
		savePayment : function(data) {
			return $http.post('cotizations/'+ data.cotization_id +'/save_payment', data);
		}
	}
}]);

app.factory('PaymentTypeService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('payment_types/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('payment_types/'+ data.id, data) : $http.post('payment_types', data);
		},
		read     : function(data) {
			return $http.get('payment_types?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('payment_types/'+ data.id);
		}
	}
}]);

app.factory('PaymentService', ['$http', function($http) {
	return {
		get      : function (data) {
			return $http.get('payments/'+ data.id);
		},
		save     : function (data) {
			return (data.id) ? $http.patch('payments/'+ data.id, data) : $http.post('payments', data);
		},
		read     : function(data) {
			return $http.get('payments?'+ jQuery.param(data), data);
		},
		delete   : function(data) {
			return $http.delete('payments/'+ data.id);
		},
		cancel   : function(data) {
			return $http.post('payments/'+ data.id +'/cancel');
		},
	}
}]);