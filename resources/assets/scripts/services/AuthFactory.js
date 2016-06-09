App.factory('AuthFactory', 

['$rootScope', '$http', '$timeout',

function ($rootScope, $http, $timeout) {
	var baseUrl = $rootScope.baseUrl;

	var auth = {};

	auth.login = function(credentials, callback) {
		var config = {
			url : baseUrl + 'login/attempt', 
			method: 'POST', 
			data: $.param(credentials),
		};

		auth.request(config, callback);
	};

	auth.request = function(config, callback) {
		$http(config).then(function(response) {
			$timeout(function() {
				callback(response);
			}, 1000);
		});
	};

	return auth;
}]);