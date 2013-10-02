'use strict';

pwApp.directive('liveFeed', function() {
    return {
        restrict: 'A',
        templateUrl: jsVars.pluginurl+'/postworld/templates/directives/liveFeed.html',
        replace: true,
        controller: 'pwLiveFeedController',
    };
});
