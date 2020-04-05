define([
	'angular'
], function(angular) {

	return {
		templateOptions: {
			labelProp: 'label',
			valueProp: 'value'
		},
		controller: ['$scope', '$http', '$timeout', 'magezonBuilderService', function($scope, $http, $timeout, magezonBuilderService) {
			$scope.to.loading = false;
			if ($scope.to.lazyOptions) {
				$scope.to.options = eval($scope.to.lazyOptions);
			}
			if ($scope.to.builderConfig) {
				$scope.to.loading = true;
				magezonBuilderService.getBuilderConfig($scope.to.builderConfig, function(options) {
					$timeout(function() {
						$scope.to.options = options;
						$scope.to.loading = false;
					});
				});
			}
			if ($scope.to.source) {
				var sourceUrl;
				switch($scope.to.source) {
					case 'page':
						sourceUrl = 'mgzbuilder/ajax/pageList';
						break;

					case 'category':
						sourceUrl = 'mgzbuilder/ajax/categoryList';
						break;

					case 'product':
						sourceUrl = 'mgzbuilder/ajax/productList';
						break;

					case 'block':
						sourceUrl = 'mgzbuilder/ajax/blockList';
						break;
				}
				var ajax = true;
				if ($scope.to.resultKey) {
					var cacheData = magezonBuilderService.getFromCache($scope.to.resultKey);
					if (cacheData) {
						$scope.to.options = cacheData;
						ajax = false;
					}
				}
				if (ajax && sourceUrl) {
					$scope.to.loading = true;
					$.ajax({
		                url: magezonBuilderService.getUrl(sourceUrl),
		                type:'POST',
		            	dataType: 'json',
		            	data: {
		            		form_key: window.FORM_KEY
		            	},
		                success: function(result) {
		                	$scope.$apply(function() {
		                		if ($scope.to.resultKey) {
									magezonBuilderService.saveToCache($scope.to.resultKey, result);
								}
								$scope.to.options = result;
								$scope.to.loading = false;
							});
		                }
		            });
				}
			} else if ($scope.to.url) {
				var ajax = true;
				if ($scope.to.resultKey) {
					var cacheData = magezonBuilderService.getFromCache($scope.to.resultKey);
					if (cacheData) {
						$scope.to.options = cacheData;
						ajax = false;
					}
				}
				if (ajax) {
					$scope.to.loading = true;
					$.ajax({
		                url: magezonBuilderService.getUrl($scope.to.url),
		                type:'POST',
		            	dataType: 'json',
		            	data: {
		            		form_key: window.FORM_KEY
		            	},
		                success: function(result) {
		                	$scope.$apply(function() {
		                		if ($scope.to.resultKey) {
									magezonBuilderService.saveToCache($scope.to.resultKey, result);
								}
								$scope.to.options = result;
								$scope.to.loading = false;
							});
		                }
		            });
				}
			}
		}]
	}
})