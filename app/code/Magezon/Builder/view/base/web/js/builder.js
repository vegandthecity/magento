define([
	'angular',
	'formly',
	'Magezon_Builder/js/factories/config',
	'Magezon_Builder/js/services/magezonBuilder',
	'Magezon_Builder/js/services/elementManager',
	'Magezon_Builder/js/services/profileManager',
	'Magezon_Builder/js/controllers/modalElements',
	'Magezon_Builder/js/controllers/modalElement',
	'Magezon_Builder/js/controllers/modalSetttings',
	'Magezon_Builder/js/controllers/modalTemplates',
	'Magezon_Builder/js/controllers/shortcodeModal',
	'Magezon_Builder/js/controllers/profileShortcodeModal',
	'Magezon_Builder/js/directives/magezonBuilder',
	'Magezon_Builder/js/directives/modalElement',
	'Magezon_Builder/js/directives/componentList',
	'Magezon_Builder/js/directives/component',
	'Magezon_Builder/js/directives/resizable',
	'Magezon_Builder/js/directives/builderDirectiveList',
	'Magezon_Builder/js/directives/staticInclude',
	'uiBootstrap',
	'dndLists',
	'angularChronicle',
	'angularSanitize',
	'dynamicDirective',
	'mgzUiTinymce',
	'uiCodemirror',
	'uiSelect',
	'outsideClickDirective',
	'ngStats'
], function(
	angular,
	formly,
	configProvider,
	magezonBuilderSer,
	elementManagerSer,
	profileManagerSer,
	modalElementsCtrl,
	modalElementCtrl,
	modalSettingsCtrl,
	modalTemplatesCtrl,
	shortcodeModalCtrl,
	profileShortcodeModalCtrl,
	magezonBuilderDir,
	modalElementDir,
	componentListDir,
	componentDir,
	resizableDir,
	builderDirectiveListDir,
	staticInclude,
	mgzUiTinymce,
	CodeMirror
) {
	var builder = angular.module('magezonBuilder', ['formly', 'dndLists', 'ui.bootstrap', 'Chronicle', 'ngSanitize', 'dynamicDirective', 'ui.tinymce', 'ui.codemirror', 'ui.select', 'ngOutsideClick', 'angularStats']);

	builder.config(function($sceDelegateProvider) {
		$resourceUrlWhitelist = ['self','*://localhost/**','*://www.youtube.com/**', '*://player.vimeo.com/video/**'];
		$sceDelegateProvider.resourceUrlWhitelist($resourceUrlWhitelist.concat(window.builderConfig.resourceUrlWhitelist));
	});

	builder.run(function(dynamicDirectiveManager, magezonBuilderConfig, magezonBuilderService, $q, $rootScope, $timeout) {
		angular.forEach(magezonBuilderConfig.elements, function(element) {
			var type = magezonBuilderService.getElementType(element.type);
			if (element['element']) {
				require([element['element']], function(Directive) {
					dynamicDirectiveManager.registerDirective('mgzElement' + type, Directive, 'mgz');
				});
			} else if (element['templateUrl']) {
				function Directive() {
				 	return {
				 		replace: true,
				 		templateUrl: magezonBuilderService.getViewFileUrl(element['templateUrl'])
				 	};
				}
				dynamicDirectiveManager.registerDirective('mgzElement' + type, Directive, 'mgz');
			} else {
				function Directive() {
				 	return {
      					replace: true,
				 		templateUrl: magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder/element/default.html')
				 	};
				}
				dynamicDirectiveManager.registerDirective('mgzElement' + type, Directive, 'mgz');
			}
		});

		var deferreds = [];
		angular.forEach(magezonBuilderConfig.directives, function(directive, name) {
			name = magezonBuilderService.getElementType(name);
			if (directive['element']) {
				require([directive['element']], function(Directive) {
					var deferred = $q.defer();
					deferreds.push(deferred);
					dynamicDirectiveManager.registerDirective('mgzDirective' + name, Directive, 'mgz');
				});
			} else if (directive['templateUrl']) {
				function Directive() {
				 	return {
				 		replace: true,
				 		templateUrl: magezonBuilderService.getViewFileUrl(directive['templateUrl'])
				 	};
				}
				dynamicDirectiveManager.registerDirective('mgzDirective' + name, Directive, 'mgz');
			}
		});
		magezonBuilderService.directives = magezonBuilderConfig.directives;
		$q.all(deferreds).then(function(response) {
			// $timeout(function() {
			// 	$rootScope.$broadcast('loadDirectives', true);
			// }, 500);
		});
	});

	// PROVIDER
	builder.provider('magezonBuilderConfig', configProvider);
	builder.service('magezonBuilderService', magezonBuilderSer);
	builder.service('elementManager', elementManagerSer);
	builder.service('profileManager', profileManagerSer);

	// DIRECTIVE
	builder.directive('magezonBuilder', magezonBuilderDir);
	builder.directive('magezonBuilderModalElement', modalElementDir);
	builder.directive('magezonBuilderComponentList', componentListDir);
	builder.directive('magezonBuilderComponent', componentDir);
	builder.directive('magezonBuilderResizable', resizableDir);
	builder.directive('magezonBuilderDirectiveList', builderDirectiveListDir);
	builder.directive('staticInclude', staticInclude);

	// CONTROLLER
	builder.controller('magezonBuilderModalElements', modalElementsCtrl);
	builder.controller('magezonBuilderModalElement', modalElementCtrl);
	builder.controller('magezonBuilderModalSettings', modalSettingsCtrl);
	builder.controller('magezonBuilderModalTemplates', modalTemplatesCtrl);
	builder.controller('shortcodeModal', shortcodeModalCtrl);
	builder.controller('profileShortcodeModal', profileShortcodeModalCtrl);

	return builder;
});