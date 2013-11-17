    function getOnlyDate( source ){
        var date_obj = new Date( source );
        // FORMAT : YYYY-MM-DD
        var date_format = date_obj.getFullYear() + "-" + (date_obj.getMonth() + 1) + "-" + (date_obj.getDate());
        return date_format ;
    }

    function getOnlyTime( source ){
        var date_obj = new Date( source );
        // FORMAT : 01:24:29
        var time_format = date_obj.getHours() + ":" + date_obj.getMinutes() + ":" + date_obj.getSeconds();
        return time_format ;
    }

    function compileDateTime(){
        //2013-08-30 01:24:29
        var dateTime = getOnlyDate( $scope.post_date.date_time_obj ) + " " + convert12to24($scope.post_date.time) ;
        return dateTime;
    }

    function convert24to12(timeString) {
        var hourEnd = timeString.indexOf(":");
        var H = +timeString.substr(0, hourEnd);
        var h = H % 12 || 12;
        var ampm = H < 12 ? "AM" : "PM";
        timeString = h + timeString.substr(hourEnd, 3) + " " + ampm;
        return timeString;
    }

    function convert12to24(timeStr){
        var meridian = timeStr.substr(timeStr.length-2).toLowerCase();
        var hours    = timeStr.substring(0, timeStr.indexOf(':'));
        var minutes  = timeStr.substring(timeStr.indexOf(':')+1, timeStr.indexOf(' '));
        if (meridian=='pm')
        {
            hours = (hours=='12') ? '00' : parseInt(hours)+12 ;
        }
        else if(hours.length<2)
        {
            hours = '0' + hours;
        }
        return hours+':'+minutes+':'+'00';
    }