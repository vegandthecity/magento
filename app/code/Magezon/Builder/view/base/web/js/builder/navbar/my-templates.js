define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
			replace: true,
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder/navbar/my_templates.html')
			},
			controller: ['$rootScope', '$scope', '$uibModal', function($rootScope, $scope, $uibModal) {
				$scope.openTemplatesModal = function() {
					var result = $uibModal.open({
						templateUrl: magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/modal/templates.html'),
						controller: 'magezonBuilderModalTemplates',
						controllerAs: 'mgz',
						openedClass: 'mgz-modal-open',
						windowClass: 'mgz-modal mgz-builder-element-form mgz-builder-element mgz-builder-templates'
					}).result.then(function() {}, function() {

					});
				}
			}]
		}
	}

	return directive;
});