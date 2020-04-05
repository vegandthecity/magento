define([
	'angular',
	'moment',
	'mage/collapsible'
], function(angular, moment) {

	var directive = function(magezonBuilderService) {
		return {
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_PageBuilder/js/templates/builder/element/toggle.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			link: function(scope, element) {

				scope.$watch('element', function(_element) {
					var target = $(element).find('.mgz-toggle');
					if (target.data("collapsible")) {
						target.collapsible('destroy');
					}
					target.collapsible({
						active: (_element.open ? true : false),
				        openedState: "mgz-active",
				        animate: {
				        	duration: 400,
				        	easing: "easeOutCubic"
				        },
				        collapsible: true,
				        icons: {
				        	header: _element.icon_style!='text_only' ? _element.icon : '',
				        	activeHeader: _element.icon_style!='text_only' ? _element.active_icon : ''
				        }
					});
				}, true);
			}
		}
	}

	return directive;
});