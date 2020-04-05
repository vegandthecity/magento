define([
	'jquery'
], function($) {

	return {
		controller: function($scope, magezonBuilderService, $sce) {

			$scope.ajax = true;

			$scope.disableActionButtons = function() {
	            $(".mgz-modal-footer-inner button").prop("disabled", true);
			}

			$scope.enableActionButtons = function() {
				$(".mgz-modal-footer-inner button").prop("disabled", false);
			}

			$scope.disableActionButtons();
			$.ajax({
				url: magezonBuilderService.getUrl('admin/widget/index'),
				type:'POST',
				data: {
					form_key: window.FORM_KEY
				},
				success: function(res) {
					$scope.$apply(function() {
						$scope.html = $sce.trustAsHtml(res);
						$scope.enableActionButtons();
					});
				}
			});

			$scope.$watch('html', function(value) {
				if ($scope.model[$scope.options.key] && value) {
					var widgetCode   = angular.copy($scope.model[$scope.options.key]);
					var optionValues = new Hash({});
					var widgetValue;

					// mage/adminhtml/wysiwyg/widget.js - line 287
	                widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function (match) {
	                    if (match[1] == 'type') { //eslint-disable-line eqeqeq
	                        widgetValue = match[2];
	                    } else {
	                        optionValues.set(match[1], match[2]);
	                    }
	                });

	                if (widgetValue) {
	                	$scope.disableActionButtons();
	                	var params = {
			                'widget_type': widgetValue,
			                values: optionValues
			            };
						$.ajax({
							url: magezonBuilderService.getUrl('admin/widget/loadOptions'),
							type:'POST',
							data: {
								widget: Object.toJSON(params)
							},
							success: function(result) {
								$scope.enableActionButtons();
								var widgetCode = widgetValue.gsub(/\//, '_');
								$('#select_widget_type').val(widgetCode);
								var optionsContainerId = 'widget_options_' + widgetCode;
								var optionsContainer   = $('#' + optionsContainerId);
								if (optionsContainer.length) {
									optionsContainer.html(result);
								} else {
									var html = '<div id="' + optionsContainerId + '">';
										html += result;
									html += '</div>';
									$('#widget_options').append(html);
								}
							}
						});
	                }
				}
			});

			$scope.updateValue = function() {
				var form  = $('#widget_options_form');
				var valid = true;
				form.find('.error').each(function(index, el) {
					if ($(this).is(':visible')) {
						valid = false;
					}
				});
				if (form.valid() && $scope.ajax) {
					$scope.ajax = false;
					$scope.disableActionButtons();
					$.ajax({
						url: magezonBuilderService.getUrl('admin/widget/buildWidget'),
						type:'POST',
						data: form.serialize(),
						success: function(result) {
							$scope.ajax = true;
            				$scope.enableActionButtons();
							$scope.model[$scope.options.key] = magezonBuilderService.decodeDirectives(result);
						}
					});
				}
			}

			$(document).on('click', '.rule-param-apply,.rule-param-remove,.data-grid ._clickable,#select_widget_type,.x-tree-node-el', function() {
				$scope.updateValue();
			});

			$(document).on('change', "#widget_options_form *[name^='parameters'],#select_widget_type", function() {
				if ($(this).parents('.mgz-builder-element-form').length) {
	               $scope.updateValue();
				}
			});
		}
	}
});