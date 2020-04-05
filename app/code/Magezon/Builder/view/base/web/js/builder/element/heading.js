define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
      		replace: true,
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_Builder/js/templates/builder/element/heading.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			controller: ['$scope', function($scope) {
				$scope.getHeadingText = function() {
					var html = '<' + $scope.element.heading_type + ' class="mgz-element-heading">'
						html += $scope.element.text;
					html += '</' + $scope.element.heading_type + '>';
					return html;
				}
			}]
		}
	}

	return directive;
});