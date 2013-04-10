/**
 * jQuery Ticking Clock by Max Fierke
 * Part of OpenSkedge (https://github.com/maxfierke/OpenSkedge)
 * Licensed under the GNU General Public License, version 3 or later.
 */
(function($) {
    $.fn.tickingClock = function(theDate) {
        var currentTime = new Date(theDate);
        update = function () {
            var seconds = currentTime.getSeconds();
            currentTime.setSeconds(seconds + 1);
            var hours = currentTime.getHours();
            var meridian;
            var minutes = currentTime.getMinutes();
            seconds = currentTime.getSeconds();
            if (seconds < 10) seconds = "0" + seconds;
            if (minutes < 10) minutes = "0" + minutes;
            if (hours > 11) meridian = "PM";
            else meridian = "AM";
            hours = hours % 12;
            if (hours === 0) hours = 12;
            $("#clockTime").html(hours + ":" + minutes + ":" + seconds + " " + meridian);
        };
        update();
        return this.each(function () {
            setInterval(update, 1000);
        });
    };
})(jQuery);
