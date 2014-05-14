

/*_____ _           _      ____                          
 |  ___| | __ _ ___| |__  / ___|__ _ _ ____   ____ _ ___ 
 | |_  | |/ _` / __| '_ \| |   / _` | '_ \ \ / / _` / __|
 |  _| | | (_| \__ \ | | | |__| (_| | | | \ V / (_| \__ \
 |_|   |_|\__,_|___/_| |_|\____\__,_|_| |_|\_/ \__,_|___/
                                                         
 ////////////////////////////////////////////////////////////////////////////*/

postworld.directive( 'flashCanvas',
    [  '$timeout',
    function( $timeout){
    return {
        //restrict: 'AE',
        controller: 'flashCanvasCtrl',
        link: function( $scope, element, attrs ){
            $scope.wizardState = {};

            
            // WIZARD Attribute
            attrs.$observe('flashCanvas', function(value) {
                if ( value ) {
                    $scope.loadFlashFile(value);
                }
            });

            // Append Canvas
            $scope.appendCanvas = function( canvasId ){

                if( _.isUndefined( canvasId ) )
                    canvasId = "canvasId";

                var canvas = angular.element( '<canvas id="'+ canvasId +'" width="160" height="160"></canvas>' );
                element.append( canvas );
                
                // Init the Canvas
                $timeout(
                    function(){
                        $scope.initCanvas( canvasId ); // , flashId
                    },
                    500
                    );
                

            }


        }
    };
}]);

postworld.controller('flashCanvasCtrl',
    ['$scope', '$rootScope', '$window', '$timeout', '_', 'pwData', '$log', 'flashData', 'pw',
    function($scope, $rootScope, $window, $timeout, $_, $pwData, $log, $flashData, $pw ) {

       
    $scope.loadFlashFile = function( file ){
        // file = "loops.loading-A" 

        var fileUrl = $_.getObj( $flashData.files() , file );

        var canvasId = "canvasId";
        var callback = $scope.appendCanvas( canvasId );

        $pw.loadScript( fileUrl, callback );

    };

    $scope.initCanvas = function( canvasId, flashId ) {
        canvas = document.getElementById( canvasId );
        exportRoot = new lib.loadingiox1();

        stage = new createjs.Stage(canvas);
        stage.addChild(exportRoot);
        stage.update();

        createjs.Ticker.setFPS(lib.properties.fps);
        createjs.Ticker.addEventListener("tick", stage);
    }

}]);


/*_____ _           _       ____        _        
 |  ___| | __ _ ___| |__   |  _ \  __ _| |_ __ _ 
 | |_  | |/ _` / __| '_ \  | | | |/ _` | __/ _` |
 |  _| | | (_| \__ \ | | | | |_| | (_| | || (_| |
 |_|   |_|\__,_|___/_| |_| |____/ \__,_|\__\__,_|
                                                 
 //////////////////////////////////////////////////////////*/

postworld.factory('flashData',
    ['$resource','$q','$log','$window', 'pw',
    function ($resource, $q, $log, $window, $pw) {   
    // DECLARATIONS

    return {

        files: function(){
            var baseUrl = $pw.pluginUrl('canvas/');
            return {
                "loops":{
                    "loading-A" : baseUrl + "loops/loading-A.js",
                }
            };
        },
        

    };

}]);


