(function ($) {
    $(document).ready(function () {

        function generateOpacityforRemainingRooms($calendar) {
            // Find the minimum and maximum number of remaining rooms in the calendar
            var minRemaining = Infinity;
            var maxRemaining = 0;
            $calendar.find('.day-cell').each(function () {
                var remaining = parseInt($(this).data('remaining'));
                if (remaining < minRemaining) {
                    minRemaining = remaining;
                }
                if (remaining > maxRemaining) {
                    maxRemaining = remaining;
                }
            });

            // Apply opacity based on the remaining rooms
            $calendar.find('.day-cell').each(function () {
                var remaining = parseInt($(this).data('remaining'));
                var opacity = (remaining === minRemaining) ? 1 : 0.2 + (0.8 * (remaining - minRemaining) / (maxRemaining - minRemaining));

                // Animate the opacity change using Velocity.js
                $(this).velocity({ opacity: opacity }, { duration: 1000 });
            });
        }

        // Fade in each calendar using Velocity.js with a delay and then apply the opacity
        var delay = 0; // Initialize delay
        $('.calendar-container').each(function (index, element) {
            var $calendar = $(element);
            $calendar.velocity("fadeIn", {
                duration: 700,
                delay: delay, // Apply delay
                complete: function () {
                    generateOpacityforRemainingRooms($calendar);
                }
            });
            delay += 30; // Increment delay for the next calendar
        });

    });
})(jQuery);
