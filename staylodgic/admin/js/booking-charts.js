(function ($) {
    $(document).ready(function () {
        let delayed;

        $('canvas.staylodgic-chart[data-type]').each(function() {
            
            var ctx = this.getContext('2d');
            var type = $(this).data('type');
            var data = $(this).data('data');
            var options = $(this).data('options') || {}; // Ensure options is an object

            // Define animation options
            var animationOptions = {
                animation: {
                    onComplete: () => {
                        delayed = true;
                    },
                    delay: (context) => {
                        let delay = 0;
                        if (context.type === 'data' && context.mode === 'default' && !delayed) {
                            delay = context.dataIndex * 300 + context.datasetIndex * 100;
                        }
                        return delay;
                    },
                },
            };

            // Merge animation options with existing options
            options = Object.assign({}, options, animationOptions);

            // Check if data and datasets are defined
            if (data && data.datasets) {
                // Check if the dataset requires a gradient
                data.datasets.forEach(function(dataset) {
                    if (dataset.useGradient) {
                        var gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, dataset.gradientStart);
                        gradient.addColorStop(1, dataset.gradientEnd);
                        dataset.backgroundColor = gradient;
                    }
                });
            } else {
                console.error('Chart data or datasets are undefined:', data);
            }

            // Initialize the chart
            new Chart(ctx, {
                type: type,
                data: data,
                options: options
            });
        });

        $('.staylodgic_analytics_table').each(function() {
            var exportTitle = $(this).data('export-title');
            $(this).DataTable({
                lengthChange: false,
                paging: false,
                info: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: exportTitle
                    },
                    {
                        extend: 'pdf',
                        title: exportTitle
                    },
                    'print'
                ]
            });
        });      
        
    });
})(jQuery);
