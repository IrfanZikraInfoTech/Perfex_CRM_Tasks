<script>
(function($){
  	"use strict";
    appValidateForm($('#form_add_check_in'), {
           'achieved': 'required',
           'upcoming_checkin': 'required',
    });
    var id = $('[name="id"]').val();
    var display = $('[name="display"]').val();
    var fnServerParams = {
        "id_s" : '[name="id"]'
    }
    requestGet(admin_url+'okr/highcharts_detailt_checkin/'+id).done(function(response) {
        response = JSON.parse(response);
        if(display == "checkin"){
            Highcharts.chart('container', {
                chart: {
                    type: 'area'
                },
                title: {
                    text: '<?php echo _l('total_progress'); ?>'
                },
                subtitle: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: response['recently_checkin'],
                    tickmarkPlacement: 'on',
                    title: {
                        enabled: false
                    }
                },
                yAxis: {
                    labels: {
                        format: '{value}%'
                    },
                    title: {
                        enabled: false
                    }
                },
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.percentage:.1f}</b> {point.y:,.0f} %<br/>',
                    split: true
                },
                plotOptions: {
                   
                },
                series: [ {
                    name: '<?php echo _l('progress'); ?>',
                    data: response['progress_total']
                }]
            });
        }
    });
    
    initDataTable('.table-history', admin_url + 'okr/table_history', false, false, fnServerParams, [0, 'desc']);
    var circle = $('.checkin-progress').circleProgress({fill: {
       gradient: ['#84C529', '#84c529']
     }}).on('circle-animation-progress', function(event, progress, stepValue) {
       $(this).find('strong.okr-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
     });
    $('input[id="achieved"]').change(function() { 
        var key = $(this).data('key');
        var value = $(this).val();
        $('[name="progress['+key+']"]').val(value);
        var html = '<div name="progress['+key+']" class="checkin-progress relative" data-value="'+(value/100)+'" data-size="55" data-thickness="5"><strong class="okr-percent"></strong></div>';
        $('#progress_m_'+key).html(html);

        var circle = $('.checkin-progress').circleProgress({fill: {
        gradient: ['#84C529', '#84c529']
         }}).on('circle-animation-progress', function(event, progress, stepValue) {
           $(this).find('strong.okr-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
         });
    });
    $('#sm_btn1').click(function(){
        $('[name="type"]').val(2);
    })
})(jQuery);
</script>