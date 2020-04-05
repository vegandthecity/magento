define([
	'jquery',
	'angular'
], function($, angular) {

	var directive = function(magezonBuilderService, $timeout, elementManager) {
		return {
      		replace: true,
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_PageBuilder/js/templates/builder/element/pagebuilder_template.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			controller: ['$rootScope', '$scope', function($rootScope, $scope) {
				$scope.$watch('element.template_id', function(templateId) {
					if (templateId) {
						$.ajax({
			                url: magezonBuilderService.getUrl('mgzpagebuilder/ajax/template'),
			                type:'POST',
			                data: {template_id: templateId},
			                success: function(name) {
			                	$scope.templateName = name;
			                }
			            });
					}
				});
			}]
		}
	}
	return directive;
});