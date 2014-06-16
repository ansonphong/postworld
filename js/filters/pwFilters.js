'use strict';

/*
  _____ _ _ _                
 |  ___(_) | |_ ___ _ __ ___ 
 | |_  | | | __/ _ \ '__/ __|
 |  _| | | | ||  __/ |  \__ \
 |_|   |_|_|\__\___|_|  |___/

/*////////////// ------------ FILTERS ------------ //////////////*/  

angular.module('pwFilters', []).filter('htmlToPlaintext', function() {
    return function(text) {
        return String(text).replace(/<(?:.|\n)*?>/gm, '');
    };
});


/*
  _____                           _         _____ _ _ _            
 |_   _| __ _   _ _ __   ___ __ _| |_ ___  |  ___(_) | |_ ___ _ __ 
   | || '__| | | | '_ \ / __/ _` | __/ _ \ | |_  | | | __/ _ \ '__|
   | || |  | |_| | | | | (_| (_| | ||  __/ |  _| | | | ||  __/ |   
   |_||_|   \__,_|_| |_|\___\__,_|\__\___| |_|   |_|_|\__\___|_|   

///////////////////////// TRUNCATE FILTER ///////////////////////// */
/**
 * Truncate Filter
 * @Param text
 * @Param length, default is 10
 * @Param end, default is "..."
 * @return string
 */
angular.module('TruncateFilter', []).
    filter('truncate', function ($sce) {
        return function (text, length, end) {
        	// text = $sce.parseAsHtml(text);
            if (isNaN(length))
                length = 10;

            if (end === undefined)
                end = "...";

            if (text.length <= length || text.length - end.length <= length) {
                return text;
            }
            else {
                return String(text).substring(0, length-end.length) + end;
            }

        };
    });




/*
  _____         _                         _____ _ _ _            
 |_   _|____  _| |_ __ _ _ __ ___  __ _  |  ___(_) | |_ ___ _ __ 
   | |/ _ \ \/ / __/ _` | '__/ _ \/ _` | | |_  | | | __/ _ \ '__|
   | |  __/>  <| || (_| | | |  __/ (_| | |  _| | | | ||  __/ |   
   |_|\___/_/\_\\__\__,_|_|  \___|\__,_| |_|   |_|_|\__\___|_|   
                                                                 
////////// NG-TEXTAREA-FILTER DIRECTIVE //////////// */
// Adds extended functionality to textareas
// Takes attributes : data-maxlength, data-readmore

// NEEDS REFCTOR!

/*
postworld.directive('ngTextareaFilter', function() {
        return function($scope, element, attributes) {
            var model = attributes.ngModel;
            var readmore = attributes.readmore;
                $scope.$watch( model,
                    function (){
                        var modelObjArray = model.split(".");
                        var textarea_contents = $scope[modelObjArray[0]][modelObjArray[1]];

                        ///// Filter Text Contents /////
                        // Setup Max Characters
                        if( typeof attributes.maxlength == 'undefined' ){
                            var maxChars = 40;
                        }
                        else{
                            var maxChars = attributes.maxlength;
                        }
                        // Setup Readmore Quote
                        if( typeof readmore == 'undefined' ){
                            var readMore = "";
                        }
                        else{
                            var readMore = readmore;
                        }
                        
                        
                        // If it's over the maxLength, trim it

                        if ( typeof textarea_contents !== 'undefined' ){
                            if ( textarea_contents.length > maxChars && textarea_contents.length > (maxChars-readMore.length) ){
                                textarea_contents = textarea_contents.slice(0, (maxChars-readMore.length)) + readMore;
                            }

                            // Insert new textarea_contents;
                            $scope[modelObjArray[0]][modelObjArray[1]] = textarea_contents;
                        }

                    }, 1 );
        };
    });
*/

/*
  ____                        _         _____ _ _ _            
 |  _ \  ___  _ __ ___   __ _(_)_ __   |  ___(_) | |_ ___ _ __ 
 | | | |/ _ \| '_ ` _ \ / _` | | '_ \  | |_  | | | __/ _ \ '__|
 | |_| | (_) | | | | | | (_| | | | | | |  _| | | | ||  __/ |   
 |____/ \___/|_| |_| |_|\__,_|_|_| |_| |_|   |_|_|\__\___|_|   
                                                               
///////////////////////// DOMAIN FILTER ///////////////////////// */
// Reduces a url string to just the base domain
postworld.filter( 'domain', function () {
  return function ( input ) {
    var matches,
        output = "",
        urls = /\w+:\/\/([\w|\.]+)/;
    matches = urls.exec( input );
    if ( matches !== null ) output = matches[1];
        return output;
  };
});

