define([
	'angular',
	'moment',
	'Magezon_PageBuilder/js/countdown'
], function(angular, moment) {

	var directive = function(magezonBuilderService) {
		return {
			templateUrl: function(elem, attrs) {
				var templateUrl = elem.parent().attr('template-url');
				if (!templateUrl) templateUrl = 'Magezon_PageBuilder/js/templates/builder/element/countdown.html';
				return magezonBuilderService.getViewFileUrl(templateUrl);
			},
			link: function(scope, element) {

				scope.$watch('element', function(_element) {
					var timeStr = _element.year + '-' + _element.month + '-' + _element.day + ' ' + _element.hours + ':' + _element.minutes;
					var time = moment(timeStr, 'YYYY-MM-DD HH:mm').tz(_element.time_zone);	
					var countdownSelector = $(element).find('.mgz-countdown');
					if (countdownSelector.data("countdown")) {
						countdownSelector.data("countdown").clearInterval();
						countdownSelector.find('.mgz-element-bar').css({
							'stroke-dashoffset': '',
							'width': ''
						});
						countdownSelector.countdown('destroy');
					}
					var time1 = time.isValid() ? time.format() : moment().format();
					countdownSelector.countdown({
						type:_element.layout,
						time: time1
					});
				}, true);
			}
		}
	}

	return directive;
});