<script>
(function($){
  	"use strict";

    $(document).ready(function(){
      $(window).load(function(){
          if($('.entry-content.single-post-content').length > 0){ 
              var wrap = $('.entry-content.single-post-content'); 
              var dodai_post = wrap.height();
              var dodai_max = 800;
              if(dodai_post > dodai_max){ 
                  wrap.addClass('tooglereadmore'); 
                  wrap.append(function(){
                      return '<div class="readmore_postcontent"><a title="Xem thêm" href="javascript:void(0);">Xem thêm</a></div>';
                  });
                  $('body').on('click','.readmore_postcontent', function(){ 
                      wrap.removeClass('tooglereadmore');
                      $('body .readmore_postcontent').remove();
                  });
              }
          }
      });
  })
	$('.progress').rateCircle({
      size: 40,
      lineWidth: 5,
      fontSize: 13,
      referenceValue: 100
    });
    appValidateForm($('#form_add_check_in'), {
           'achieved': 'required',
           'upcoming_checkin': 'required',
    });
    var id = $('[name="id"]').val();
    var display = $('[name="display"]').val();
    var fnServerParams = {
        "id_s" : '[name="id"]'
    }
    if('<?php echo html_entity_decode($tab); ?>' == 'checkin'){
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
    }
    initDataTable('.table-history', admin_url + 'okr/table_history', false, false, fnServerParams, [0, 'desc']);
    var circle = $('.checkin-progress').circleProgress({fill: {
       gradient: ['#84C529', '#84c529']
     }}).on('circle-animation-progress', function(event, progress, stepValue) {
       $(this).find('strong.okr-percent').html(parseInt(100 * stepValue) + '<i>%</i>');
     });
    $('input[id="achieved"]').change(function() { 
        var key = $(this).data('key');
        var min = $(this).data('min');
        var max = $(this).data('max');
        var value = parseFloat($(this).val().replace(new RegExp(',', 'g'),""));
        if(value <= min){
          alert_float('warning', 'The value must be greater than or equal 0');
        }
        if(value > max){
          value = max;
          $(this).val(max);
          alert_float('warning', 'Value must be less than or equal to '+max);
        }
        if(isNaN(value)){
          value = 0;
        }
        var target = parseFloat($('[name="target['+key+']"]').val());

        value = (value/target)*100;
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
    

    $('#complete_okrs').click(function(event) {
            var recently_checkin = $('[name="recently_checkin"]').val();
            if($(this).is(":checked")){
                $('#upcoming_checkin').parent().parent().addClass('hide');
                $('#upcoming_checkin').val(recently_checkin);
            }else{
                $('#upcoming_checkin').parent().parent().removeClass('hide');
                $('#upcoming_checkin').val('');
            }
        });

   $('#upcoming_checkin').on('change', function(){
    var special_characters = $('[name="special_characters"]').val();

    var dateFrom = $('[name="circulation_from"]').val();
    var dateTo = $('[name="circulation_to"]').val();
    var dateCheck = $(this).val();


    var d1 = dateFrom.split(special_characters);
    var d2 = dateTo.split(special_characters);
    var c = dateCheck.split(special_characters);

    var from = new Date(d1[2], parseInt(d1[1])-1, d1[0]);  // -1 because months are from 0 to 11
    var to   = new Date(d2[2], parseInt(d2[1])-1, d2[0]);
    var check = new Date(c[2], parseInt(c[1])-1, c[0]);
    if((check >= from && check <= to) == false){
      $('[name="upcoming_checkin"]').css('color', 'red');
      alert_float('danger', apps.lang.upcoming_checkin_alert);
    }else{
      $('[name="upcoming_checkin"]').css('color', 'black');
    }
    
   }) 
   $('.task-func').click(function(){
    if(document.URL == '<?php echo admin_url('okr/checkin_detailt/'.$id.'?tab=history'); ?>'){
      $('.history').remove();
    }

    if(document.URL == '<?php echo admin_url('okr/checkin_detailt/'.$id.'?tab=checkin'); ?>' || document.URL == '<?php echo admin_url('okr/checkin_detailt/'.$id); ?>'){
      $('.checkin').remove();
    }

   })

   $('.task_add').click(function(){
      var id = $(this).parents('tr').data('key-result');
      $('#add-task-key-result input[name="id"]').val(id);
      $('select[name="task_key_result"]').selectpicker('refresh');
      
      $('#add-task-key-result').modal('show');
    })

   
})(jQuery);

</script>)