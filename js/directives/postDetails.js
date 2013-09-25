/**
 * Created by Michel on 9/22/13.
 */
'use strict';

pwApp.directive('postDetails', function() {
    return {
        restrict: 'E',
        templateUrl: jsVars.pluginurl+'/postworld/templates/directives/postDetails.html',
        replace: true,
        scope: {
            post: "="
        }

    };
});
