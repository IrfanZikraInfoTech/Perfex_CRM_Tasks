<script>
(function($) {
    "use strict";
    rec_chart_by_status('rec_plan_chart_by_status',<?php echo html_entity_decode($rec_plan_chart_by_status); ?>, <?php echo json_encode(_l('rec_plan_chart_by_status')); ?>);
    rec_chart_by_status('rec_campaign_chart_by_status',<?php echo html_entity_decode($rec_campaign_chart_by_status); ?>, <?php echo json_encode(_l('rec_campaign_chart_by_status')); ?>);
    //declare function variable radius chart
    function rec_chart_by_status(id, value, title_c){
        Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'inherit',
                fontWeight:'normal'
            }
        }
       });
        Highcharts.chart(id, {
            chart: {
                backgroundcolor: '#fcfcfc8a',
                type: 'variablepie'
            },
            accessibility: {
                description: null
            },
            title: {
                text: title_c
            },
            credits: {
                enabled: false
            },
            tooltip: {
                pointFormat: '<span style="color:{series.color}">'+<?php echo json_encode(_l('invoice_table_quantity_heading')); ?>+'</span>: <b>{point.y}</b> <br/> <span>'+<?php echo json_encode(_l('rec_ratio')); ?>+'</span>: <b>{point.percentage:.0f}%</b><br/>',
                shared: true
            },
             plotOptions: {
                variablepie: {
                    dataLabels: {
                        enabled: false,
                        },
                    showInLegend: true
                }
            },
            series: [{
                minPointSize: 10,
                innerSize: '20%',
                zMin: 0,
                name: <?php echo json_encode(_l('invoice_table_quantity_heading')); ?>,
                data: value,
                point:{
                      events:{
                          click: function (event) {
                             if(this.statusLink !== undefined)
                             {
                               window.location.href = this.statusLink;

                             }
                          }
                      }
                  }
            }]
        });
    }
})(jQuery);
</script>