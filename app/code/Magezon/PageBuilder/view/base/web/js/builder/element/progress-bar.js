define([
	'jquery',
	'angular',
	'Magezon_PageBuilder/js/number-counter'
], function($, angular) {

	var directive = function($timeout, magezonBuilderService) {
		return {
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_PageBuilder/js/templates/builder/element/progress_bar.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			link: function(scope, element) {
				scope.initCounter = function() {
					var _element = scope.element;
					$timeout(function() {
						var speed = _element.speed ? parseFloat(_element.speed) * 1000 : 0;
						var delay = _element.speed ? parseFloat(_element.delay) : 0;
						angular.forEach(_element.items, function(item, index) {
							var uid = 'mgz-single-bar' + index;
							var selector = element.find('#' + uid);
							if (selector.data("numberCounter")) {
								selector.css({
									'stroke-dashoffset': '',
									'width': ''
								});
								selector.numberCounter('destroy');
							}
							selector.numberCounter({
								layout: 'bars',
								type: 'percent',
								number: parseFloat(item.value),
								speed: speed,
								delay: delay
							});
						});
					}, 500);
				}
				scope.$watchCollection('element.items', function(items) {
					scope.initCounter();
				}, true);

				scope.$watch('element', function(_element) {
					scope.initCounter();
				}, true);
			}
		}
	}

	return directive;
});