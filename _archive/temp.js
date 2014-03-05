
    $scope.setDateRange = function( set, dateObj, offset ){
        // set = 'min' / 'max'
        // dateObj = a JS Date Object
        // offset = how much to offset in milliseconds from given time

        // USAGE
        // setDateRange();                              // Resets both to null
        // setDateRange( 'min' );                       // Sets min to current time
        // setDateRange( 'max' );                       // Sets max to null
        // setDateRange( 'min', dateObj );              // Offsets the dateObj to the minDate
        // setDateRange( 'max', dateObj, '100000' );    // Offsets the dateObj by 100000 milliseconds and sets the maxDate
        alert('returnTime');
        ///// DEFAULT SETTINGS ////
        if( _.isUndefined(set) ){
            // Defaults
            $scope.minDate = null;
            $scope.maxDate = null;
            return true;
        }
        
        ///// MINIMUM SETTINGS /////
        if( set == 'min'  ){
            // If dateObj is undefined, set as current time
            if( _.isUndefined( dateObj ) ){
                $scope.minDate = new Date();
                return true;
            }

            // If Date Object is defined but not offset
            // Set the setting to the given time
            if( _.isUndefined( offset ) ){
                $scope.minDate = dateObj;
                return true;
            }

            // If a offset number is given
            // Subtract that many milliseconds
            if( _.isNumber(offset) ){
                var localDateObj = new Date(dateObj);
                var parsedDateObj = Date.parse(localDateObj);
                var newTime = parsedDateObj - offset;
                var returnTime = new Date(newTime);
                alert(returnTime);
                return returnTime

            }
            return false;
            
        }


        ///// MAXIMUM SETTINGS /////
        if( set == 'max'  ){
            // If dateObj is undefined, set as current time
            if( _.isUndefined( dateObj ) ){
                $scope.maxDate = null;
                return true;
            }

            // If Date Object is defined but not offset
            // Set the setting to the given time
            if( _.isUndefined( offset ) ){
                $scope.maxDate = dateObj;
                return true;
            }

            // If a offset number is given
            // Add that many milliseconds
            if( _.isNumber(offset) ){

            }
            return false;
            
        }
        



        //if(   _.isObject($scope.post.post_meta.related_post) )
        //  alert('object');



    };
