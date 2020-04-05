define([
	'jquery',
	'angular',
	'Magezon_Builder/js/factories/FormlyUtils'
], function($, angular, FormlyUtils) {

	return {
		templateOptions: {
			labelProp: 'label',
			valueProp: 'value'
		},
		controller: ['$rootScope', '$scope', '$http', 'magezonBuilderService', 'elementManager', function($rootScope, $scope, $http, magezonBuilderService, elementManager) {

			if ($scope.to.lazyOptions) {
				$scope.to.options = eval($scope.to.lazyOptions);
			}

			$scope.loadTemplates = function() {
				$.ajax({
					url: magezonBuilderService.getUrl($scope.to.url),
					type:'POST',
					data: {
						form_key: window.FORM_KEY
					},
					success: function(res) {
						var options = res;
						if ($scope.to.resultKey) {
							$rootScope['ajaxData'][$scope.to.resultKey] = options
						}
						$scope.to.options = options;
						$scope.$digest();
					}
				});
			}

			if ($scope.to.url) {
				$scope.loadTemplates();
			}

			$scope.$on('loadTemplates', function() {
				if ($scope.to.resultKey && $rootScope['ajaxData'].hasOwnProperty($scope.to.resultKey)) {
					delete $rootScope['ajaxData'][$scope.to.resultKey];
				}
				$scope.loadTemplates();
			});

			$scope.prepareElements = function(item) {
				if (!item.hasOwnProperty('elements') && item.profile) {
					item['elements'] = elementManager.prepareElements(item.profile.elements);
				}
			}

			$scope.openElements = function(item) {
				item.init = true;
				item['active'] = !item['active'];
				$scope.prepareElements(item);
				angular.forEach($scope.to.options, function(_item, index) {
					if (_item['value'] != item['value']) {
						_item['active'] = false;
					}
				})
			}

		    $scope.importItem = function(item) {
		    	$scope.prepareElements(item);
		    	var newItem = angular.copy(item);
		    	if (item.file && !item.profile) {
		    		$http.get(item.file).then(function(res, status, xhr) {
		    			if (res.data.elements) {
		    				newItem['elements'] = elementManager.prepareElements(res.data.elements);
		    			} else {
		    				newItem['elements'] = elementManager.prepareElements(res.data.profile.elements);
		    			}
				    	if (newItem['elements']) {
				    		angular.forEach(newItem['elements'], function(element, index) {
				    			$rootScope.profile.elements.push(angular.copy(element));
				    		});
						}
		    		});
		    	}
		    	if (item.elements) {
		    		angular.forEach(item.elements, function(element, index) {
		    			$rootScope.profile.elements.push(angular.copy(element));
		    		});
		    	}
				$('.mgz-modal-action-close').trigger('click');
				$('.modal-backdrop').remove();
				$('.mgz-builder-templates').remove();
		    }
		}]
	}
})