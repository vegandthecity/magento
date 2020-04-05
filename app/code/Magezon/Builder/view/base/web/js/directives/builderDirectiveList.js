define([
	'jquery',
	'angular'
], function (jQuery, angular) {

	var directive = function(magezonBuilderService, $compile, $timeout, $q) {
		return {
			scope: {
				region: '@',
				htmlTag: '@',
				element: '=?'
			},
			link: function(scope, element, attrs) {
				var htmlTag = scope.htmlTag ? scope.htmlTag : 'div';

				var deferreds = [];
				var items     = {};

				angular.forEach(magezonBuilderService.directives, function(directive, name) {
					if (scope.region == directive.displayArea && (!directive.hasOwnProperty('disabled') || !directive.disabled) && (!directive.loaded || directive.dynamic)) {
						var additionalClasses = directive.additionalClasses ? ' ' + directive.additionalClasses : '';
						if (directive['element']) {
							var deferred;
							deferred = jQuery.Deferred();
	                    	deferreds.push(deferred);
							require([directive['element']], function(Directive) {
								var html = '<' + htmlTag + ' class="magezon-builder-directive' + additionalClasses + '" element="element" dynamic-directive element-name="mgz-directive-' + name + '" sort-order="' + (directive['sortOrder'] ? directive['sortOrder'] : 0) + '"></' + htmlTag + '>';
								items[name] = html;
								directive['loaded'] = true;
								deferred.resolve();
							});
						} else {
							var html = '<' + htmlTag + ' class="magezon-builder-directive' + additionalClasses + '" element="element" dynamic-directive element-name="mgz-directive-' + name + '" sort-order="' + (directive['sortOrder'] ? directive['sortOrder'] : 0) + '"></' + htmlTag + '>';
							items[name] = html;
							directive['loaded'] = true;
						}
					}
				});

				jQuery.when.apply(jQuery, deferreds).done(function () {
					angular.forEach(items, function(item) {
						var template = angular.element(item);
						var html     = $compile(template)(scope.$new(true));
						element.append(html);
					});
				}.bind(this));
			}
		}
	}
	return directive;
});