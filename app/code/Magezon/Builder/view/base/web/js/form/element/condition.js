define([
	'jquery'
], function($) {

	$(document).on('click', '.rule-param-remove', function() {
		$('.rule-tree').trigger('change');
	});

	return {
		controller: ['$scope', '$sce', 'magezonBuilderService', function($scope, $sce, magezonBuilderService) {
			$scope.to.loading = true;
			$scope.id         = magezonBuilderService.uniqueid();
			$scope.status     = true;

			$scope.disableActionButtons = function() {
	            $(".mgz-modal-footer-inner button").prop("disabled", true);
			}

			$scope.enableActionButtons = function() {
				$(".mgz-modal-footer-inner button").prop("disabled", false);
			}


			$scope.loadConditionsValue = function() {
				if ($scope.status) {
					$scope.status = false;
					$scope.disableActionButtons();
					$.ajax({
						url: magezonBuilderService.getUrl('mgzbuilder/ajax/conditionsValue'),
						type:'POST',
						dataType: 'json',
						data: {
							values: $('.mgz-elements-list form').serialize(),
							field: $scope.options.key
						},
						success: function(res) {
							if (res.status) {
								$scope.$apply(function() {
									$scope.model[$scope.options.key] = res.value;
								});
							}
							$scope.enableActionButtons();
							$scope.status = true;
						}
					});
				}
			}

			$.ajax({
				url: magezonBuilderService.getUrl('mgzbuilder/ajax/conditions'),
				type:'POST',
				dataType: 'json',
				data: {
					conditions: $scope.model[$scope.options.key],
					id: $scope.id
				},
				success: function(res) {
					if (res.message) {
						alert(res.message);
					}
					if (res.status) {
						$scope.$apply(function() {
							$scope.html       = $sce.trustAsHtml(res.html);
							$scope.to.loading = false;

							$('#' + $scope.id).on('click', '.rule-param-remove,.rule-param-apply,.data-grid ._clickable,#select_widget_type', function() {
								$scope.loadConditionsValue();
							});

							$('#' + $scope.id).on('change', '.element-value-changer,.rule-tree', function() {
								$scope.loadConditionsValue();
							});
						});
					}
				}
			});
		}]
	}
});