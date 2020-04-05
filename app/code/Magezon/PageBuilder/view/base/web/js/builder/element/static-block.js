define([
	'jquery'
], function($) {

	var directive = function($sce, magezonBuilderService) {
		return {
      		replace: true,
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_PageBuilder/js/templates/builder/element/static-block.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			controller: ['$rootScope', '$scope', function($rootScope, $scope) {
				$scope.$watch('element.block_id', function(value) {
					if (value) {
						$scope.element.component.loading = true;
						$.ajax({
			                url: magezonBuilderService.getUrl('mgzbuilder/ajax/info'),
			                type:'POST',
			                data: {
			                	type: 'block',
			                	id: $scope.element.block_id
			                },
			                success: function(data) {
			                	console.log(data);
			                	$scope.name = data.title;
			                	$scope.element.component.loading = false;
			                }
			            });
					} else {
						$scope.name = '';
					}
				});
			}]
		}
	}

	return directive;
});