define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
			replace: true,
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder/navbar/add.html')
			},
			controller: ['$rootScope', '$scope', function($rootScope, $scope) {
				$scope.openModal = function() {
					$rootScope.$broadcast('openElementsModal', true);
				}
			}]
		}
	}

	return directive;
});