define([
	'angular',
	'Magezon_Builder/js/carousel'
], function(angular) {

	var directive = function(magezonBuilderService) {
		return {
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_PageBuilder/js/templates/builder/element/testimonials.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			controller: ['$rootScope', '$scope', '$timeout', function($rootScope, $scope, $timeout) {
				$scope.owlSettings = {};
				$scope.owlItems = {};

				$scope.$watch('element.items', function(items) {
					$scope.owlItems = angular.copy(items);
				}, true);

			    $scope.$watch('element', function(_element) {
					$scope.additionalClass = '';
			    	var _element = $scope.element;
					var config = {
						nav: _element.owl_nav ? true : false,
						dots: _element.owl_dots ? true : false,
						autoplayHoverPause: _element.owl_autoplay_hover_pause ? true : false,
						autoplay: _element.owl_autoplay ? true : false,
						autoplayTimeout: parseFloat(_element.owl_autoplay_timeout),
						lazyLoad: _element.owl_lazyload ? true : false,
						loop: _element.owl_loop ? true : false,
						margin: parseFloat(_element.owl_margin),
						autoHeight: _element.owl_auto_height ? true : false,
						rtl: _element.owl_rtl ? true : false,
						center: _element.owl_center ? true : false,
						slideBy: _element.owl_slide_by,
						stagePadding: _element.owl_stage_padding,
						animateIn: _element.owl_animate_in,
						animateOut: _element.owl_animate_out,
						navText: ['<i class="fas mgz-fa-angle-left"/>','<i class="fas mgz-fa-angle-right"/>']
					};

					config['responsive'] = {
		                0: {'items': parseInt(_element.owl_item_xs)},
		                576: {'items': parseInt(_element.owl_item_sm)},
		                768: {'items': parseInt(_element.owl_item_md)},
		                992: {'items': parseInt(_element.owl_item_lg)},
		                1200: {'items': parseInt(_element.owl_item_xl)}
		            };

		            $scope.additionalClass += ' mgz-carousel-nav-position-' + _element.owl_nav_position;
		            $scope.additionalClass += ' mgz-carousel-nav-size-' + _element.owl_nav_size;

		            if (_element.owl_dots_insie) {
		            	$scope.additionalClass += ' mgz-carousel-dot-inside';
		            }
		            $scope.additionalClass += ' mgz-testimonials';
		            $scope.additionalClass += ' mgz-testimonials-' + _element.testimonial_type;

					$scope.owlSettings = config;
			    }, true);
				
			}]
		}
	}

	return directive;
});