define([
	'jquery',
	'minicolors'
], function($) {

	return {
		controller: ['$scope', '$timeout', function($scope, $timeout) {

			var pickerSettings = {
				theme: 'bootstrap',
	            keywords: 'transparent, initial, inherit',
	            opacity: true,
	            format: 'rgb'
			}
			Object.extend(pickerSettings, $scope.to.minicolors)

			$timeout(function() {
			    $('#' + $scope.id).minicolors(pickerSettings);
			}, 500);

			$scope.$watch('model.' + $scope.options.key, function(value) {
				$('#' + $scope.id).minicolors(pickerSettings);
			});
		}]
	}
});