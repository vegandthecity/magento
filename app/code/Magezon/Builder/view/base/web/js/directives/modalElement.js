define([
	'angular'
], function (angular) {

	var directive = function(magezonBuilderService) {
		return {
			scope: {
				element: '=element'
			},
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/modal/element.html')
			}
		}
	}

	return directive;
});