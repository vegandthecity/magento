define([
	'jquery',
	'underscore',
    'uiRegistry'
], function($, _, registry) {

	return {
		controller: ['$scope', '$timeout', 'magezonBuilderService', function($scope, $timeout, magezonBuilderService) {

			var callbacks = [];

			var actions = [
				{
					targetName: 'linkbuilder_form.linkbuilder_form.link_modal',
					actionName: 'toggleModal'
				},
				{
					targetName: 'linkbuilder_form.linkbuilder_form.link_modal.link',
					actionName: 'render'
				},
				{
					targetName: 'linkbuilder_form.linkbuilder_form.link_modal.link',
					actionName: 'updateData'
				}
			];

			_.each(actions, function (action) {
                callbacks.push({
                    action: registry.async(action.targetName),
                    args: _.union([action.actionName], action.params),
                    targetName: action.targetName
                });
            });

			//https://github.com/kvz/locutus/blob/master/src/php/strings/stripslashes.js
			function removeslashes(str) {
				return (str + '')
				.replace(/\\(.?)/g, function (s, n1) {
					switch (n1) {
						case '\\':
						return '\\'
						case '0':
						return '\u0000'
						case '':
						return ''
						default:
						return n1
					}
				});
            }

            $scope.getLinkParams = function(link) {
				var params = {
					type: 'custom',
					url: '',
					id: 0,
					title: '',
					extra: '',
					nofollow: 0,
					blank: 0
		    	};
		    	if (link) {
					if (link.indexOf('{{mgzlink') === -1) {
						params['url']  = link;
						params['type'] = 'custom';
					} else {
						var parseAttributesString = function (attributes) {
				            var result = {};
				            // Decode &quot; entity, as regex below does not support encoded quote
				            attributes = attributes.replace(/&quot;/g, '"');
				            attributes.gsub(
				                /(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/,
				                function (match) {
				                    result[match[1]] = match[2];
				                }
				            );
				            return result;
				        }
						link.gsub(/\{\{mgzlink(.*?)\}\}/i, function (match) {
							params = parseAttributesString(match[1]);
						});
					}
				}
				if (params['url']) {
					params['url'] = removeslashes(params['url']);
				}
				return params;
            }

			$scope.selectUrl = function() {
				var linkData = $scope.getLinkParams($scope.model[$scope.options.key]);
				linkData['target'] = $scope.id;
				window.mgzlinkbuilder = linkData;
				_.each(callbacks, function (callback) {
                    callback.action.apply(callback.action, callback.args);
                });
			}

			$scope.$watch('model.' + $scope.options.key, function(value) {
				var params = $scope.getLinkParams(value);
				$scope.title = params.title;
				switch (params['type']) {
					case 'category':
						$scope.type = 'Category';
						break;

					case 'product':
						$scope.type = 'Product';
						break;

					case 'page':
						$scope.type = 'Page';
						break;

					case 'custom':
						$scope.type = 'Custom Url';
						$scope.linkName = params.url;
						break;
				}
				if (params['type'] == 'category' || params['type'] == 'product' || params['type'] == 'page') {
					$scope.linkName = '';
					$scope.to.loading = true;
					$.ajax({
						url: magezonBuilderService.getUrl('mgzbuilder/ajax/info'),
						type:'POST',
						dataType: 'json',
						data: {
							type: params['type'],
							id: params['id']
						},
						success: function(res) {
							if (res) {
								$timeout(function() {
									switch (params['type']) {
										case 'category':
										$scope.linkName = res.name;
										break;

										case 'product':
										$scope.linkName = res.name;
										break;

										case 'page':
										$scope.linkName = res.title;
										break;
									}
								});
							}
							$scope.to.loading = false;
						}
					});
				}
			}, true);
		}]
	}
});