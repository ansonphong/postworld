/*
   _        _____    _ _ _     ____           _   
  | |   _  | ____|__| (_) |_  |  _ \ ___  ___| |_ 
 / __) (_) |  _| / _` | | __| | |_) / _ \/ __| __|
 \__ \  _  | |__| (_| | | |_  |  __/ (_) \__ \ |_ 
 (   / (_) |_____\__,_|_|\__| |_|   \___/|___/\__|
  |_|                                             
////////// ------------ EDIT POST SERVICE ------------ //////////*/  
/*
postworld.service('pwEditPost', ['$log', function ($log) {
        return {
            pwGetPost: function(){
                return {
                    //post_id : 24,
                    post_author: 1,
                    post_title : "Hello Space",
                    post_name : "hello_space",
                    post_type : "blog",
                    post_status : "publish",
                    post_format : "video",
                    post_class : "contributor",
                    link_url : "http://youtube.com/",
                    post_permalink : "http://realitysandwich.com/",
                    tax_input : {
                        topic : ["healing","body"],
                        section : ["psi"],
                    },
                    tags_input : "tag1, tag2, tag3",
                };
            },
            
        };
    }]);
*/








function editPost($scope) {

    ////////// DATE & TIME PROCESSING //////////
    // The date format takes in the post_date_gmt, GMT/UTC
    // It is converted it to the local time zone
    // The user transforms the time in local time zone
    // Then it is saved to the model in GMT/UTC

    ///// PAD NUMBER /////
    // Pads a number with 00000s up to the number of digits provided
    Number.prototype.pad = function(num,digits) {
        return ('0000000000' + this).slice((num || digits) * -1);
    }

    ///// TIME ZONE ABBR /////
    function clientTimeZoneAbbr (dateInput) {
    // Friendly timezone abbreviations in client-side JavaScript
        var dateObject = dateInput || new Date(),
            dateString = dateObject + "",
            tzAbbr = (
                // Works for the majority of modern browsers
                dateString.match(/\(([^\)]+)\)$/) ||
                // IE outputs date strings in a different format:
                dateString.match(/([A-Z]+) [\d]{4}$/)
            );
     
        if (tzAbbr) {
            // Old Firefox uses the long timezone name (e.g., "Central
            // Daylight Time" instead of "CDT")
            tzAbbr = tzAbbr[1].match(/[A-Z]/g).join("");
        }
        return tzAbbr;
    };

    function WPtoDateObject(dateString){
        //var dateString = "2010-08-09 01:02:03";
        var reggie = /(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/;
        var dateArray = reggie.exec(dateString); 

        var dateObject = new Date(
            (+dateArray[1]),
            (+dateArray[2])-1, // Careful, month starts at 0!
            (+dateArray[3]),
            (+dateArray[4]),
            (+dateArray[5]),
            (+dateArray[6])
        );
        return dateObject;
    }



    // SET UTC TIME OBJECT
    // Casts a time object from the specified time zone into UTC time
    // ie. timeString = 2013-09-16 18:24:16
    // ie. timezone = PDT (optional) (default:UTC)
    function setTimeObjectUTC ( timeString, timezone ){
        if( typeof timezone === "undefined" )
            var timezone = " UTC";
        else
            var timezone = " " + timezone;
        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        // USE : WPtoDateObject
        // Somehow change the time zone

        //var timeString = WPtoDateObject( timeString );
        //timeString = timeString.toString();
        //var timzoneField = "(" + timezone + ")";
        //timeString = timeString.replace(/\(.+?\)/g, timzoneField );
        var timeString = timeString + timezone;
        var timeObject = new Date( timeString );
        //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        return timeObject;
    }

    // TIME OBJECT TO WORDPRESS TIME STRING : ie. 2013-10-12 18:24:16
    // Converts a JS time object into a WP string in local time
    // timeObject = a Javascript time object
    // type = 'array'/'string' - how to return the result
    // UTC = boolean (optional) - return the UTC time, if false returns the local time
    function timeObjectToWP ( timeObject, returnType, UTC ){

        if( UTC == true ){
            var year = timeObject.getUTCFullYear();
            var month = (timeObject.getUTCMonth() +1) ;
            var date = timeObject.getUTCDate();
            var hours = timeObject.getUTCHours();
            var minutes = timeObject.getUTCMinutes();
            var seconds = timeObject.getUTCSeconds();
        } else {
            var year = timeObject.getFullYear();
            var month = (timeObject.getMonth() +1) ;
            var date = timeObject.getDate();
            var hours = timeObject.getHours();
            var minutes = timeObject.getMinutes();
            var seconds = timeObject.getSeconds();
        }

        if( typeof returnType === "undefined" )
            var returnType = "string";

        if( returnType == "string" ){
            return year + "-" + month + "-" + date + " " + hours + ":" + minutes + ":" + seconds;
        }
        else if( returnType == "array" ){
            var timeArray = [];
            timeArray.push( year, month, date, hours, minutes, seconds );
            return timeArray;
        }

    }

    function getPostDate( dateField ){ // post_date_gmt / post_date
        if ( typeof dateField === 'undefined' ){
            // RETURN THE CURRENT DATE
            var currentDate = new Date();
            return currentDate;
        }
        else
            // RETURN THE GIVEN DATE
            return new Date( WPtoDateObject( dateField ) );
    }


    //var gmt_time = "2013-09-16 18:24:16";
    // Get the post's time in GMT/UTC

    $scope.post_date_gmt = getPostDate( $scope.post_data.post_date_gmt );


    //$scope.test_num = 42334;
    //alert( $scope.test_num.pad(8) );

    // PARSE DATE TEST
    //$scope.parsed_date = parseDateObject("2010-08-09 01:02:03");
    //alert( $scope.post_date_gmt );

    // TIME IN : local time zone
    $scope.timeString = timeObjectToWP( $scope.post_date_gmt, 'string' );
    $scope.timeArray = timeObjectToWP( $scope.post_date_gmt, 'array' );

    // TIME IN : UTC
    $scope.timeStringUTC = timeObjectToWP ( setTimeObjectUTC( $scope.timeString, clientTimeZoneAbbr() ), "string", 1 );

    // SANITIZE THE UPDATED FIELDS TO POST OBJECT
    $scope.$watch( "timeArray",
        function (){
            var timeArray = $scope.timeArray;
            var dateObj = $scope.post_date_gmt;

            // YEAR
            timeArray[0] = parseInt(timeArray[0]);
            if ( timeArray[0] > 9999 )
                timeArray[0] = timeArray[0].slice(0,4);
            if ( timeArray[0] < 1 || isNaN( timeArray[0] ) )
                timeArray[0] = dateObj.getUTCFullYear();

            // MONTH
            timeArray[1] = parseInt(timeArray[1]);
            if ( timeArray[1] > 12 )
                timeArray[1] = 12;
            if ( timeArray[1] < 1 || isNaN( timeArray[1] ) )
                timeArray[1] = (dateObj.getUTCMonth()+1);
            // Pad with 00
            //if ( timeArray[1] < 10 )
            //    timeArray[1] = timeArray[1].pad(2);

            // DATE
            timeArray[2] = parseInt(timeArray[2]);
            if ( timeArray[2] > 31 )
                timeArray[2] = 31;
            if ( timeArray[2] < 1 || isNaN( timeArray[2] ) )
                timeArray[2] = 1;

            // HOURS
            timeArray[3] = parseInt(timeArray[3]);
            if ( timeArray[3] > 23 )
                timeArray[3] = 23;
            if ( timeArray[3] < 0 || isNaN( timeArray[3] ) )
                timeArray[3] = 0;

            // MINUTES
            timeArray[4] = parseInt(timeArray[4]);
            if ( timeArray[4] > 59 )
                timeArray[4] = 59;
            if ( timeArray[4] < 0 || isNaN( timeArray[4] ) )
                timeArray[4] = 0;

            // SECONDS
            timeArray[5] = parseInt(timeArray[5]);
            if ( timeArray[5] > 59 )
                timeArray[5] = 59;
            if ( timeArray[5] < 0 || isNaN( timeArray[5] ) )
                timeArray[5] = 0;

            // TIME IN : local time zone
            $scope.timeArray = timeArray;
            $scope.timeString = timeArray[0] + "-" + timeArray[1] + "-" + timeArray[2] + " " + timeArray[3] + ":" + timeArray[4] + ":" + timeArray[5];

            // TIME IN : UTC
            $scope.timeStringUTC = timeObjectToWP ( setTimeObjectUTC( $scope.timeString, clientTimeZoneAbbr() ), "string", 1 );

        },1);


        // WRITE THE UPDATED DATE TO POST OBJECT
        $scope.$watch( "timeStringUTC",
            function ( newValue, oldValue ){
                $scope.post_data.post_date_gmt = $scope.timeStringUTC;
            });



}




/*
    <!-- POST TIME -->
    <div class="time_input">
      <input ng-model="timeArray[0]" type="text" ng-minlength="2" ng-maxlength="4" maxlength="4" class="year">/
      <input ng-model="timeArray[1]" type="text" ng-minlength="1" ng-maxlength="2" maxlength="2" class="month">/
      <input ng-model="timeArray[2]" type="text" ng-minlength="1" ng-maxlength="2" maxlength="2" class="date">
      @
      <input ng-model="timeArray[3]" type="text" ng-minlength="1" ng-maxlength="2" maxlength="2" class="hour">:
      <input ng-model="timeArray[4]" type="text" ng-minlength="1" ng-maxlength="2" maxlength="2" class="minute">:
      <input ng-model="timeArray[5]" type="text" ng-minlength="1" ng-maxlength="2" maxlength="2" class="second">
      <br>
      timeArray : {{timeArray}}<br>
      timeString : {{timeString}}<br>
      timeStringUTC : {{timeStringUTC}}
    </div>
*/