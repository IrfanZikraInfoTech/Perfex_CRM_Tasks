<script>
$(function () {
    'use strict';

  fix_kanban_height(290, 360);
  initKnowledgeBaseTableArticles();
  $(".groups").sortable({
    connectWith: ".article-group",
    helper: 'clone',
    appendTo: '#kan-ban',
    placeholder: "ui-state-highlight-kan-ban-kb",
    revert: true,
    scroll: true,
    scrollSensitivity: 50,
    scrollSpeed: 70,
    start: function (event, ui) {
      $('body').css('overflow', 'hidden');
    },
    stop: function (event, ui) {
      $('body').removeAttr('style');
    },
    update: function (event, ui) {
      if (this === ui.item.parent()[0]) {
        var articles = $(ui.item).parents('.article-group').find('li');
        i = 1;
        var order = [];
        $.each(articles, function () {
          i++;
          order.push([$(this).data('article-id'), i]);
        });
        setTimeout(function () {
          $.post(admin_url + 'hr_profile/knowledge_base_q_a/update_kan_ban', {
            order: order,
            groupid: $(ui.item.parent()[0]).data('group-id')
          });
        }, 100);
      }
    }
  }).disableSelection();

  $('.groups').sortable({
    cancel: '.sortable-disabled'
  });

  setTimeout(function () {
    $('.kb-kan-ban').removeClass('hide');
  }, 200);

  $(".container-fluid").sortable({
    helper: 'clone',
    item: '.kan-ban-col',
    cancel: '.sortable-disabled',
    update: function (event, ui) {
      var order = [];
      var status = $('.kan-ban-col');
      var i = 0;
      $.each(status, function () {
        order.push([$(this).data('col-group-id'), i]);
        i++;
      });
      var data = {}
      data.order = order;
      $.post(admin_url + 'hr_profile/knowledge_base_q_a/update_groups_order', data);
    }
  });
  // Status color change
  $('body').on('click', '.kb-kan-ban .cpicker', function () {
    var color = $(this).data('color');
    var group_id = $(this).parents('.panel-heading-bg').data('group-id');
    $.post(admin_url + 'hr_profile/knowledge_base_q_a/change_group_color', {
      color: color,
      group_id: group_id
    });
  });
  $('.toggle-articles-list').on('click', function () {
    var list_tab = $('#list_tab');
    if (list_tab.hasClass('active')) {
      list_tab.css('display', 'none').removeClass('active');
      $('.kan-ban-tab').css('display', 'block');
      fix_kanban_height(290, 360);
      mainWrapperHeightFix();
    } else {
      list_tab.css('display', 'block').addClass('active');
      $('.kan-ban-tab').css('display', 'none');
    }
  });
});

function initKnowledgeBaseTableArticles() {
    'use strict';

    if($( ".article_change_icon" ).hasClass( "fa fa-th-list" )){
      $( ".article_change_icon" ).removeClass("fa fa-th-list");
      $( ".article_change_icon" ).addClass("fa fa-archive");
    }else{
      $( ".article_change_icon" ).removeClass("fa fa-archive");
      $( ".article_change_icon" ).addClass("fa fa-th-list");
    }
  
  var KB_Articles_ServerParams = {
      "group_id"    : "select[name='group[]']",
  };

  $.each($('._hidden_inputs._filters input'), function () {
    KB_Articles_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
  });
  $('._filter_data').toggleClass('hide');
  initDataTable('.table-articles', window.location.href, [0], [0], KB_Articles_ServerParams, [0, 'desc']);

  $('#group').on('change', function() {
      $('.table-articles').DataTable().ajax.reload().columns.adjust().responsive.recalc();
    });
}

function send_mail_support(slug, curator, staff_email){
    'use strict';

  $('#support__form').modal('show');
  appValidateForm($('#mail_form_knowledge_base'), {
   content: 'required', subject:'required',email:'required'});

   $('input[name="slug"]').val(slug);
   $('input[name="curator"]').val(curator);
   $('input[name="show_staff_email"]').val(staff_email);

}

  function staff_bulk_actions(){
    'use strict';

    $('#table_contract_bulk_actions').modal('show');
  }

   // Leads bulk action
   function staff_delete_bulk_action(event) {
    'use strict';

    if (confirm_delete()) {
      var mass_delete = $('#mass_delete').prop('checked');

      if(mass_delete == true){
        var ids = [];
        var data = {};

        data.mass_delete = true;
        data.rel_type = 'hrm_kb-articles';

        var rows = $('#table-kb-articles').find('tbody tr');
        $.each(rows, function() {
          var checkbox = $($(this).find('td').eq(0)).find('input');
          if (checkbox.prop('checked') === true) {
            ids.push(checkbox.val());
          }
        });

        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
          $.post(admin_url + 'hr_profile/hrm_delete_bulk_action', data).done(function() {
            window.location.reload();
          }).fail(function(data) {
            $('#table_contract_bulk_actions').modal('hide');
            alert_float('danger', data.responseText);
          });
        }, 200);
      }else{
        window.location.reload();
      }

    }
   }
</script>