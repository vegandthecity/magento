define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
			replace: true,
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder/helper.html')
			},
			controller: ['$rootScope', '$scope', function($rootScope, $scope) {
				$scope.addRowElement = function(columns) {
					$rootScope.$broadcast('rootAddRowComponent', columns);
				}

				$scope.addRootElement = function(type, additionalData) {
					var builderElement = $rootScope.elementManager.getElement(type);
					if (builderElement) {
						builderElement['additionalData'] = additionalData;
						$rootScope.$broadcast('rootAddComponent', builderElement);
					}
				}

				$scope.openModal = function() {
					$rootScope.$broadcast('openElementsModal', true);
				}
			}]
		}
	}

	return directive;
});