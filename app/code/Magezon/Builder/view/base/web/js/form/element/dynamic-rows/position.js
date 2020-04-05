define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		link: function(scope, element, attrs, ctrl, $timeout) {
			$(element).find('input').change(function(event) {
				scope.$emit('sortDynamicItems', {
					key: scope.options.key
				});
			});
		}
	}
});