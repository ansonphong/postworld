'use strict';

/*
  _____ _ _ _                
 |  ___(_) | |_ ___ _ __ ___ 
 | |_  | | | __/ _ \ '__/ __|
 |  _| | | | ||  __/ |  \__ \
 |_|   |_|_|\__\___|_|  |___/

/*////////////// ------------ FILTERS ------------ //////////////*/  

postworld.filter('htmlToPlaintext', function() {
    return function(text) {
        return String(text).replace(/<(?:.|\n)*?>/gm, '');
    };
});


postworld.filter('reverse', function() {
  return function(items) {
    return items.slice().reverse();
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
postworld.filter('truncate', function ($sce) {
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

