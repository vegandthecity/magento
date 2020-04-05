define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService, $timeout) {
		return {
			replace: true,
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder/navbar/shortcode.html')
			},
			controller: ['$rootScope', '$scope', '$uibModal', function($rootScope, $scope, $uibModal) {
				$scope.openProfileShortcodeModal = function() {
					var result = $uibModal.open({
						templateUrl: magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/modal/form.html'),
						controller: 'profileShortcodeModal',
						controllerAs: 'mgz',
						openedClass: 'mgz-modal-open',
						windowClass: 'mgz-modal mgz-builder-element-form'
					}).result.then(function() {}, function() {});
				}
			}]
		}
	}

	return directive;
});