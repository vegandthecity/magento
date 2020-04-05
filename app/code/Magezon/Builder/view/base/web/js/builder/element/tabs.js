define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_Builder/js/templates/builder/element/tabs.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			controller: ['$rootScope', '$scope', function($rootScope, $scope) {

				$scope.element.component.dndDisabled = true;

				angular.forEach($scope.element.elements, function(element, index) {
					if (index == 0) {
						element.component.hide = false;
						element.component.visible = true;
					} else {
						element.component.hide = true;
						element.component.visible = false;
					}
				});

				$scope.$on('afterAddComponent', function(event, element) {
					$scope.activeEventElement(element);
				});

				$scope.$on('afterCloneComponent', function(event, element) {
					$scope.activeEventElement(element);
				});

				$scope.$on('afterDropComponent', function(event, element) {
					$scope.activeEventElement(element);
				});

				$scope.activeEventElement = function(element) {
					var index = $scope.element.elements.indexOf(element);
					if (index !== -1) {
						$scope.activeElement(element);
					}
				}

				$scope.$on('afterRemoveComponent', function(event, element) {
					$scope.activeFirstElement();
				});

				$scope.activeElement = function(element) {
					if (!element) return;
					angular.forEach($scope.element.elements, function(el, index) {
						if (el !== element) {
							el.component.hide = true;
						}
					});
					element.component.hide = false;
					element.component.visible = true;
				}

				$scope.activeFirstElement = function() {
					var active = true;
					angular.forEach($scope.element.elements, function(element) {
						if (!element.component.hide) {
							active = false;
						}
					});
					if (active) {
						$scope.activeElement($scope.element.elements[0]);
					}
				}

			}]
		}
	}

	return directive;
});