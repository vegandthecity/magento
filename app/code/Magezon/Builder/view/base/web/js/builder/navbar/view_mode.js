define([
	'angular'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
			replace: true,
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder/navbar/view_mode.html')
			},
			controller: ['$rootScope', '$scope', function($rootScope, $scope) {

				$scope.viewMode  = magezonBuilderService.getViewMode();

				$scope.viewModes = {
					xl: {
						title: 'Desktop',
						icon: 'mgz-icon mgz-icon-desktop'
					},
					lg: {
						title: 'Tablet Landscape',
						icon: 'mgz-icon mgz-icon-tablet-landscape'
					},
					md: {
						title: 'Tablet Portrait',
						icon: 'mgz-icon mgz-icon-tablet-portrait'
					},
					sm: {
						title: 'Mobile Landscape',
						icon: 'mgz-icon mgz-icon-mobile-landscape'
					},
					xs: {
						title: 'Mobile Portrait',
						icon: 'mgz-icon mgz-icon-mobile-portrait'
					}
				}

				$scope.changeViewMode = function(viewMode) {
					$scope.viewMode = viewMode;
					magezonBuilderService.setViewMode(viewMode);
				}

				$scope.$watch('viewMode', function(viewMode) {
					$rootScope.$broadcast('changedViewMode', viewMode);
				});

				$scope.$on('setViewMode', function(event, viewMode) {
					$scope.changeViewMode(viewMode);
				});
			}]
		}
	}

	return directive;
});