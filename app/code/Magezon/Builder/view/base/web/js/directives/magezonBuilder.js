define([
	'jquery',
	'underscore',
	'Magezon_Builder/js/factories/FormlyUtils'
], function ($, _, FormlyUtils) {

	var magezonBuilderDir = function(magezonBuilderService, profileManager, $timeout, $rootScope, $compile) {
		return {
			scope: {
				profile: '='
			},
    		replace: true,
			restrict: 'AE',
			templateUrl: function(elem, attrs) {
				return attrs.templateUrl ? attrs.templateUrl : magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/builder.html')
			},
			controller: ['$rootScope', '$scope', '$uibModal', 'Chronicle', 'elementManager', function($rootScope, $scope, $uibModal, Chronicle, elementManager) {

				window.getWatchers = function getWatchers(root) {
					root = angular.element(root || document.documentElement);
					var watcherCount = 0;
					function getElemWatchers(element) {
						var isolateWatchers = getWatchersFromScope(element.data().$isolateScope);
						var scopeWatchers = getWatchersFromScope(element.data().$scope);
						var watchers = scopeWatchers.concat(isolateWatchers);
						angular.forEach(element.children(), function (childElement) {
							watchers = watchers.concat(getElemWatchers(angular.element(childElement)));
						});
						return watchers;
					}
					function getWatchersFromScope(scope) {
						if (scope) {
							return scope.$$watchers || [];
						} else {
							return [];
						}
					}
					return getElemWatchers(root);
				}

				$rootScope.fullscreen = false;
				$scope.status = false;
				$scope.import = false;

				$scope.$on('onDragstart', function(event, draggingElement) {
					$rootScope.draggingElement = draggingElement;
				});

				$scope.$on('onDragend', function(event, draggingElement) {
					$rootScope.draggingElement = '';
				});

				$scope.$on('onCanceled', function(event, draggingElement) {
					$rootScope.draggingElement = '';
				});

				$rootScope.canUndo = false;
				$rootScope.reUndo  = false;
				$rootScope.numChronicle = Chronicle.record('profile', $scope);
			    $scope.canUndoRedo = function() {
			        $rootScope.canUndo = $rootScope.numChronicle.canUndo();
			        $rootScope.canRedo = $rootScope.numChronicle.canRedo();
			        if ($rootScope.canUndo || $rootScope.canRedo) {
			        	$rootScope.$broadcast('exportShortcode', true);
			    	}
			    };
			    $rootScope.numChronicle.addOnAdjustFunction($scope.canUndoRedo);
			    $rootScope.numChronicle.addOnUndoFunction($scope.canUndoRedo);
			    $rootScope.numChronicle.addOnRedoFunction($scope.canUndoRedo);

				$scope.openModal = function() {
					var result = $uibModal.open({
						templateUrl: magezonBuilderService.getViewFileUrl('Magezon_Builder/js/templates/modal/elements.html'),
						controller: 'magezonBuilderModalElements',
						controllerAs: 'mgz',
						openedClass: 'mgz-modal-open',
						windowClass: 'mgz-modal mgz-modal-elements'
					}).result.then(function() {}, function() {
						magezonBuilderService.setTargetElement(null);
						magezonBuilderService.setTargetAction(null);
					});
				}

				$scope.$on('openElementsModal', function(event) {
					$scope.openModal();
				});

				$scope.getTargetContent = function() {
					if (typeof tinyMCE.get($scope.profile.targetId) === 'object' && tinyMCE.get($scope.profile.targetId)) {
						value = tinyMCE.get($scope.profile.targetId).getContent();
						value = value.replace(/(<p[^>]+?>|<p>&nbsp;<\/p>)/img, "");
		            } else {
		            	value = $('#' + $scope.profile.targetId).val();
		            }
		            return value;
				}

				$scope.setTargetContent = function(content) {
					if (typeof tinyMCE.get($scope.profile.targetId) === 'object' && tinyMCE.get($scope.profile.targetId)) {
		                tinyMCE.get($scope.profile.targetId).setContent(content);
		            }
		            $('#' + $scope.profile.targetId).val(content);
		            $('#' + $scope.profile.targetId).trigger('change');
				}

				$scope.parseAttributesString = function (attributes) {
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

		        $rootScope.$on('exportShortcode', function(event, draggingElement) {
					$scope.exportShortcode();
				});

				$scope.exportShortcode = function() {
					if (!$scope.import && $scope.status) {
						profileManager.updateTargetContent();
						$scope.loadStyles();
					}	
				}

				$scope.$on('importShortcode', function(event, status) {
					$scope.status = true;
					$scope.import = true;
					$scope.importShortcode();
				});

				$scope.importShortcode = function() {
					var content        = profileManager.getTargetContent();
					var profileKey     = $rootScope.builderConfig.profile.key;
					var startKey       = '[' + profileKey + ']';
					var endKey         = '[/' + profileKey + ']';
					var currentProfile = {};
					if (profileKey) {
						var start = content.indexOf(startKey);
						var end   = content.indexOf(endKey, start + endKey.length);
						var code  = content.substring(start + startKey.length, end);
						if (end > start) {
							currentProfile = JSON.parse(code);
						}
					}

					if (!currentProfile.elements) {
						try {
							currentProfile = JSON.parse(content);
						} catch (err) {}
					}

					$scope.$apply(function() {
						$rootScope.currentProfile = currentProfile;
						if (currentProfile.elements) {
							try {
								$scope.profile.elements = $scope.importElement(currentProfile.elements);
							} catch (err) {
								$scope.profile.elements = [];
							}
							$scope.import = false;
						} else {
							var defaultValues = {};
							if ($rootScope.builderConfig.profile.hasOwnProperty('defaultSettings')) {
								defaultValues = $rootScope.builderConfig.profile.defaultSettings;
							}
							$rootScope.currentProfile = defaultValues;
							$scope.import             = false;
							if (content) {
								$scope.profile.elements = [];
								$rootScope.$broadcast('rootAddComponent', {
									type: 'text',
									openModal: false,
									additionalData: {
										content: content
									}
								});
							} else {
								$scope.status = true;
								$scope.exportShortcode();
							}
						}
					});
				}

				$scope.importElement = function(elements) {
					var newElements = [];
					angular.forEach(elements, function(element) {
						var builderElement   = elementManager.elements[element.type];
						if (!builderElement) return;

						if (!$rootScope.builderConfig.profile.editorMode) {
							element['id'] = FormlyUtils.getUniqueId();
						}
						element['component'] = elementManager.getComponentDefault(element.type);

						if (element.hasOwnProperty('lg_size') && !element.hasOwnProperty('xl_size')) {
							element['xl_size'] = element['lg_size'];
						}

						if (element.elements) {
							element.elements = $scope.importElement(element.elements);
						}
						newElements.push(element);
					});
					return newElements;
				}

				$rootScope.$on('loadStyles', function(event, draggingElement) {
					$scope.loadStyles();
				});

		        $scope.loadStyles = function() {
		        	if ($rootScope.builderConfig.loadStylesUrl && $scope.profile.elements && $scope.profile.elements.length) {
			        	$.ajax({
			                url: magezonBuilderService.getUrl($rootScope.builderConfig.loadStylesUrl),
			                type:'POST',
			                data: {
			                	profile: profileManager.toString(false)
			                },
	                    	dataType: 'json',
			                success: function(res) {
								if (res.message) {
									alert(res.message);
								}
								if (res.status) {
									$('#' + $scope.profile.targetId + '-styles').html(res.html);
								}

								var height = $(window).height();
								$(document).find('.mgz-row-full-height').each(function(index, el) {
									$(this).css('min-height', height);
									$(this).children('.mgz-element-inner').css('min-height', height);
									$(this).children().children('.mgz-element-row-content').css('min-height', height);
								});
								$('body').trigger('magezonPageBuilderUpdated');
			                }
			            });
			        }
		        }
		        $timeout(function() {
		        	$('.' + $rootScope.builderConfig.htmlId + '-spinner').remove();
		        	$scope.status = true;
					$scope.import = true;
					var content    = profileManager.getTargetContent();
					var profileKey = $rootScope.builderConfig.profile.key;
					var startKey   = '[' + profileKey + ']';
					var endKey     = '[/' + profileKey + ']';
					var currentProfile = {};
					if (profileKey) {
						var start = content.indexOf(startKey);
						var end   = content.indexOf(endKey, start + endKey.length);
						var code  = content.substring(startKey.length, end - start);

						// Contain more than code START1[magezon_pagebuilder]{{elements:}}[/magezon_pagebuilder]ENDABC
						if ((end > start && (content && (content.length == (end - start + endKey.length)))) || !content) {
							$scope.importShortcode();
							$('.' + $rootScope.rootId).addClass('mgz-deactive-builder');
							$('.' + $rootScope.rootId).removeClass('mgz-active-builder');
						} else {
							$('.' + $rootScope.rootId).addClass('mgz-active-builder');
							$('.' + $rootScope.rootId).removeClass('mgz-deactive-builder');
						}
					} else {
						$scope.importShortcode();
					}
		        }, 100);
			}]
		}
	};

	return magezonBuilderDir;
});