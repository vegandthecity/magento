define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
			replace: true,
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder/navbar/settings.html')
			},
			controller: ['$rootScope', '$scope', '$uibModal', function($rootScope, $scope, $uibModal) {
				$scope.openModal = function () {
					var result = $uibModal.open({
						templateUrl: magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/modal/form.html'),
						controller: 'magezonBuilderModalSettings',
						controllerAs: 'mgz',
						openedClass: 'mgz-modal-open',
						windowClass: 'mgz-modal mgz-builder-element-form mgz-builder-element'
					}).result.then(function() {}, function() {});
		        }
			}]
		}
	}

	return directive;
});