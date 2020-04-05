define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService, $rootScope, $timeout) {
		return {
      		replace: true,
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_Builder/js/templates/builder/element/call_to_action.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			controller: ['$scope', function($scope) {
				$scope.getTitleHtml = function() {
					var element = $scope.element;
					var html = '<' + element.title_type + ' class="mgz-cta-title ' + ( element.content_hover_animation ? 'mgz-animated-item--' + element.content_hover_animation : '' ) + '">'
						html += element.title;
					html += '</' + element.title_type + '>';
					return html;
				}
			}],

			link: function(scope, element) {
				scope.$watch('element.image', function(image) {
					$(element).find('.mgz-bg').css('background-image', 'url(' + $rootScope.magezonBuilderService.getImageUrl(image) + ')');
				});
			} 
		}
	}

	return directive;
});