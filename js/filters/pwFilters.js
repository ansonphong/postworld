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
  _____ _                     _                 _____ _ _ _            
 |_   _(_)_ __ ___   ___     / \   __ _  ___   |  ___(_) | |_ ___ _ __ 
   | | | | '_ ` _ \ / _ \   / _ \ / _` |/ _ \  | |_  | | | __/ _ \ '__|
   | | | | | | | | |  __/  / ___ \ (_| | (_) | |  _| | | | ||  __/ |   
   |_| |_|_| |_| |_|\___| /_/   \_\__, |\___/  |_|   |_|_|\__\___|_|   
                                  |___/                                
///////////////////////// TIME AGO FILTER ///////////////////////// */

angular.module('TimeAgoFilter', []).
    filter('timeago', function() {
    	
	    	function pad(number, length){
			    var str = "" + number;
			    while (str.length < length) {
			        str = '0'+str;
			    };
			    return str;
			};

			var offset = new Date().getTimezoneOffset();
			offset = ((offset<0? '+':'-')+ // Note the reversed sign!
			          pad(parseInt(Math.abs(offset/60)), 2)+
			          pad(Math.abs(offset%60), 2));
    		console.log('offset',offset);
    		//var thedate = "2013-10-26T20:00:32.000Z";
    		//console.log('time',new Date(thedate));
        return function(input, p_allowFuture) {
            var substitute = function (stringOrFunction, number, strings) {
                    var string = $.isFunction(stringOrFunction) ? stringOrFunction(number, dateDifference) : stringOrFunction;
                    var value = (strings.numbers && strings.numbers[number]) || number;
                    return string.replace(/%d/i, value);
                },
                nowTime = (new Date()).getTime(),
                date = (new Date(input+"Z")).getTime(),
                //refreshMillis= 6e4, //A minute
                allowFuture = p_allowFuture || false,
                strings= {
                    prefixAgo: null,
                    prefixFromNow: null,
                    suffixAgo: "ago",
                    suffixFromNow: "from now",
                    seconds: "less than a minute",
                    minute: "about a minute",
                    minutes: "%d minutes",
                    hour: "about an hour",
                    hours: "about %d hours",
                    day: "a day",
                    days: "%d days",
                    month: "about a month",
                    months: "%d months",
                    year: "about a year",
                    years: "%d years"
                },
                dateDifference = nowTime - date,
                words,
                seconds = Math.abs(dateDifference) / 1000,
                minutes = seconds / 60,
                hours = minutes / 60,
                days = hours / 24,
                years = days / 365,
                separator = strings.wordSeparator === undefined ?  " " : strings.wordSeparator,
            
                // var strings = this.settings.strings;
                prefix = strings.prefixAgo,
                suffix = strings.suffixAgo;
                // console.log('time=',input);

            if (allowFuture) {
                if (dateDifference < 0) {
                    prefix = strings.prefixFromNow;
                    suffix = strings.suffixFromNow;
                }
            }

            words = seconds < 45 && substitute(strings.seconds, Math.round(seconds), strings) ||
            seconds < 90 && substitute(strings.minute, 1, strings) ||
            minutes < 45 && substitute(strings.minutes, Math.round(minutes), strings) ||
            minutes < 90 && substitute(strings.hour, 1, strings) ||
            hours < 24 && substitute(strings.hours, Math.round(hours), strings) ||
            hours < 42 && substitute(strings.day, 1, strings) ||
            days < 30 && substitute(strings.days, Math.round(days), strings) ||
            days < 45 && substitute(strings.month, 1, strings) ||
            days < 365 && substitute(strings.months, Math.round(days / 30), strings) ||
            years < 1.5 && substitute(strings.year, 1, strings) ||
            substitute(strings.years, Math.round(years), strings);

            return $.trim([prefix, words, suffix].join(separator));
            // conditional based on optional argument
            // if (somethingElse) {
            //     out = out.toUpperCase();
            // }
            // return out;
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

