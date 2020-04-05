define([
	'jquery',
	'codemirror'
], function($, CodeMirror) {

	return {
		controller: ['$scope', '$timeout', function($scope, $timeout) {
			$timeout(function() {
				$scope.refreshEditor = true;
			}, 200);
		}]
	}
});