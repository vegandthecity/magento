define([
	'jquery',
	'angular',
	'Magezon_PageBuilder/js/number-counter'
], function($, angular) {

	var directive = function(magezonBuilderService) {
		return {
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_PageBuilder/js/templates/builder/element/number_counter.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			controller: ['$rootScope', '$scope', '$timeout', function($rootScope, $scope, $timeout) {
				$scope.getCounterStyles = function() {
					var styles = {};
					if ($scope.element.layout == 'circle') {
						styles['width'] = parseInt($scope.element.circle_size);
						styles['height'] = parseInt($scope.element.circle_size);
					}
					return styles;
				}

				$scope.getCircumference = function() {
					var element = $scope.element;
					return ($rootScope.parseInt(element.circle_size) - ($rootScope.parseInt(element.circle_dash_width) * 2)) * $rootScope.$window.Math.PI;
				}

				$scope.getViewBox = function() {
					var element = $scope.element;
					return '0 0 ' + parseInt(element.circle_size) + ' ' + parseInt(element.circle_size);
				}
			}],
			link: function(scope, element) {
				scope.$watch('element', function(_element) {
					var max = _element.max;
					if (!max) max = _element.number;
					var speed = _element.speed ? parseFloat(_element.speed) * 1000 : 0;
					var delay = _element.delay ? parseFloat(_element.delay) : 0;
					var radius = (parseInt(_element.circle_size) / 2) - parseInt(_element.circle_dash_width);
					var numberCounterSelector = $(element).find('.mgz-numbercounter-content');
					if (numberCounterSelector.data("numberCounter")) {
						numberCounterSelector.find('.mgz-element-bar').css({
							'stroke-dashoffset': '',
							'width': ''
						});
						numberCounterSelector.find('.mgz-numbercounter-bar').css({
							'width': ''
						});
						numberCounterSelector.numberCounter('destroy');
					}
					setTimeout(function() {
						numberCounterSelector.numberCounter({
							layout: _element.layout,
							type:_element.number_type,
							number: parseFloat(_element.number),
							max: parseFloat(max),
							speed: speed,
							delay: delay,
							circleDashWidth: parseFloat(_element.circle_dash_width),
							radius: radius
						});
					}, 500);
				}, true);
			}
		}
	}

	return directive;
});