define([
	'jquery',
	'angular',
	'Magezon_Builder/js/factories/FormlyUtils'
], function($, angular, FormlyUtils) {

	var registerTypes = function(array, formlyConfigProvider) {
		for (var i = 0; i < array.length; i++) {
			var elem = array[i];
			// DynamicRows
			if (elem.templateOptions.children) {
				registerTypes(elem.templateOptions.children, formlyConfigProvider);
			}
			if (elem.hasOwnProperty('fieldGroup')) {
				formlyConfigProvider.setWrapper({
					name: elem.wrapper,
					templateUrl: elem.templateOptions.templateUrl
				});
				registerTypes(elem['fieldGroup'], formlyConfigProvider);
			} else {
				var newType = {
					name: elem.type,
					templateUrl: elem.templateOptions.templateUrl
				};
				if (elem.defaultOptions) {
					newType.defaultOptions = Object.assign({}, elem.defaultOptions);
				}
				formlyConfigProvider.setType(newType);
			}
			if (elem.templateOptions.wrapperTemplateUrl) {
				formlyConfigProvider.setWrapper({
					name: elem.data.wrapperType,
					templateUrl: elem.templateOptions.wrapperTemplateUrl
				});
			}
		}
	}

	var array_diff = function(arr1) {
		var retArr = {}
		var argl = arguments.length
		var k1 = ''
		var i = 1
		var k = ''
		var arr = {}

		  	arr1keys: for (k1 in arr1) {
		  		for (i = 1; i < argl; i++) {
		  			arr = arguments[i]
		  			for (k in arr) {
		  				if (arr[k] === arr1[k1]) {
		          			continue arr1keys;
		      			}
		  			}
		  			retArr[k1] = arr1[k1]
				}
			}
		return retArr
	}

	var array_values = function (input) {
		var tmpArr = []
		var key = ''

		for (key in input) {
			tmpArr[tmpArr.length] = input[key]
		}

		return tmpArr
	}

	return function(formlyConfigProvider) {
		var elements      = {};
		var groups        = {};
		var $customFields = {};
		var $config       = {};
		var $directives   = {};

		return {
			registerConfig: function(config) {
				$config = config;
			},

			registerGroup: function(name, group) {
				if (!groups[name]) {
					groups[name] = group;
				} else {
					angular.extend(groups[name], group);
				}
			},

			registerElement: function(name, element) {
				if (element.hasOwnProperty('visible') && element.visible || !element.hasOwnProperty('visible')) {
					if (element.tabs) {
						element.tabs = FormlyUtils.processFields(element.tabs, 'children');
						registerTypes(element.tabs, formlyConfigProvider);
					}
					if (!elements[name]) {
						var processFields = function(array) {
							angular.forEach(array, function(row) {
								if (row.fieldGroup) {
									processFields(row.fieldGroup);
								}
							})
						}
						processFields(element.tabs);
						elements[name] = element;
					} else {
						angular.extend(elements[name], element);
					}
				}
			},

			processAllowTypes: function() {
				$allTypes      = [];
				angular.forEach(elements, function(element, index) {
					$allTypes.push(element.type);
				});

				$childrenTypes = [];
	            angular.forEach(elements, function(element, index) {
					$allowedTypes  = element.allowed_types;
					$excludedTypes = element.excluded_types;

	                if (angular.isString($allowedTypes)) {
	                    element.allowed_types = $allowedTypes.split(',');
	                }

	                if (!element.allowed_types && $excludedTypes) {
	                    if (angular.isString($excludedTypes)) {
	                        $excludedTypes = $excludedTypes.split(',');
	                    }
	                    $allowedTypes = array_diff($allTypes, $excludedTypes);
	                    element.allowed_types = array_values($allowedTypes);
	                }

	                if (element.children) {
	                    $childrenTypes.push(element.children);
	                }
	            });

	            $validTypes = array_diff($allTypes, $childrenTypes);
	            angular.forEach(elements, function(element, index) {
	                $allowedTypes  = element.allowed_types;
	                if ($allowedTypes) {
	                    if ($.inArray( element.children, $allowedTypes) === -1) {
							$_allowedTypes        = array_diff($allowedTypes, $childrenTypes);
							element.allowed_types = array_values($_allowedTypes);
	                    }
	                } else {
	                    element.allowed_types = $validTypes;
	                }
	            });
			},

			registerDirectives: function(name, directive) {
				$directives[name] = directive;
			},

			$get: function() {
				return {
					elements: elements,
					groups: groups,
					customFields: $customFields,
					config: $config,
					directives: $directives
				}
			}
		}
	}
});