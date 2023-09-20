    (function(){
      "use strict";
      $( document ).ready(function() {
        $.Shortcuts.stop();
      });
      appValidateForm($('#add-edit-folder-form'), {
        'name': 'required',
      });

      appValidateForm($('#share-form'), {
        'id': 'required',
      });

      appValidateForm($('#related-form'), {
        'id': 'required',
      });

      $('.add_folder_button').on('click', function(){
        $('#AddFolderModal .update-new').addClass('hide');
        $('#AddFolderModal .add-new').removeClass('hide');
        $('#AddFolderModal input[name="name"]').val('');
        $('#AddFolderModal input[name="id"]').val('');
      })
      var staff_share_value = '';
      var client_share_value = '';

      $('.add_share_button').on('click', function(){
        $('input[name="value-hidden"]').val('');
        var id = $('#ShareModal [name="id"]').val();

        $('.remove_box_information_review').click();
        $('.remove_box_information_review_client').click();
        
        if(id != ''){
          $('#ShareModal [name="update"]').val("true");
          requestGet(admin_url + 'spreadsheet_online/get_my_folder/'+ id).done(function(response) {
            response = JSON.parse(response);
            $('.share-row').removeClass('hide');
            if(response.group_share_client == 2){
              var hide_old = $('input[name="value-hidden"]').val();
              hide_old != '' ? $('input[name="value-hidden"]').val(hide_old+ ',' +response.group_share_client) : $('input[name="value-hidden"]').val(response.group_share_client);
              $('input[name="group_share_client"]').prop( "checked",true);
              $('.choosee-staff').removeClass('hide');
              if(response.client_groups_share || response.clients_share){
                var client_groups_share = response.client_groups_share.split(",");
                var clients_share = response.clients_share.split(",");
                $.each(clients_share, function (index, value) {
                  client_share_value = clients_share[index];      
                  if(index > 0){
                    $('.new_box_information_review_client').click();
                    $('select[name="client_groups_share['+index+']"]').val(client_groups_share[index]).change();
                    requestGet(admin_url + 'spreadsheet_online/get_my_folder_get_hash/client/' + value + '/' + id).done(function(response_client) {
                      response_client = JSON.parse(response_client);
                      $('select[name="role_client['+index+']"]').val(response_client.role ? response_client.role : 1).change();
                    })
                  }
                  else{
                    onchane_handle_client(index, client_share_value);
                    $('select[name="client_groups_share['+index+']"]').val(client_groups_share[index]).change();
                    requestGet(admin_url + 'spreadsheet_online/get_my_folder_get_hash/client/' + value + '/' + id).done(function(response_client) {
                      response_client = JSON.parse(response_client);
                      $('select[name="role_client['+index+']"]').val(response_client.role ? response_client.role : 1).change();
                    })
                  }
                });
              }

            }else{
              $('input[name="group_share_client').removeAttr('checked');
            }

            if(response.group_share_staff == 1){
              var hide_old = $('input[name="value-hidden"]').val();
              hide_old != '' ? $('input[name="value-hidden"]').val(hide_old+ ',' +response.group_share_staff) : $('input[name="value-hidden"]').val(response.group_share_staff);
              $('input[name="group_share_staff"]').prop( "checked",true);
              $('.choosee-customer').removeClass('hide');
              if(response.departments_share || response.staffs_share){
                var departments_share = response.departments_share.split(",");
                var staffs_share = response.staffs_share.split(",");
                $.each(staffs_share, function (index, value) {
                  staff_share_value = staffs_share[index];      
                  if(index > 0){
                    $('.new_box_information_review').click();
                    $('select[name="departments_share['+index+']"]').val(departments_share[index]).change();
                    requestGet(admin_url + 'spreadsheet_online/get_my_folder_get_hash/staff/' + value + '/' + id).done(function(response_staff) {
                      response_staff = JSON.parse(response_staff);
                      $('select[name="role_staff['+index+']"]').val(response_staff.role ? response_staff.role : 1).change();
                      staff_share_value = staffs_share[index];
                    })
                  }
                  else{
                    $('select[name="departments_share['+index+']"]').val(departments_share[index]).change();
                    onchane_handle_department(index, staff_share_value);
                    $('select[name="staffs_share['+index+']"]').val(staffs_share[index]).change();
                    requestGet(admin_url + 'spreadsheet_online/get_my_folder_get_hash/staff/' + value + '/' + id).done(function(response_staff) {
                      response_staff = JSON.parse(response_staff);
                      $('select[name="role_staff['+index+']"]').val(response_staff.role ? response_staff.role : 1).change();
                    })
                  }

                });
              }
            }else{
              $('input[name="group_share_staff').removeAttr('checked');
            }
            $('.test_class').click(function(){
              $('.remove_box_information_review').click();
              $('.remove_box_information_review_client').click();                       
              $('select[name="client_groups_share[0]"]').val(''); 
              $('select[name="clients_share[0]"]').val(''); 
              $('select[name="departments_share[0]"]').val(''); 
              $('select[name="staffs_share[0]"]').val('');       
              $('select[name="client_groups_share[0]"]').selectpicker('refresh'); 
              $('select[name="departments_share[0]"]').selectpicker('refresh');
              $('input[name="group_share_staff"]').prop( "checked",false);
              $('input[name="client_groups_share"]').prop( "checked",false);
            })
          })
  }else{
    $('.remove_box_information_review').click();
    $('.remove_box_information_review_client').click();
  }
})
    

    $('#ShareModal [type="submit"]').click(function() {
      var id = $('#ShareModal [name="id"]').val();
      if(id == ''){
        alert_float('warning', 'Please select a shared file or folder!');
      }
    })

    $('#RelatedModal [type="submit"]').click(function() {
      var id = $('#RelatedModal [name="id"]').val();
      if(id == ''){
        alert_float('warning', 'Please select a file or folder!');
      }
    })


    $('#spreadsheet-advanced .tr-pointer').on('click', function(){
      var id = $(this).parents("tr").data("tt-id");
      var type = $(this).parents("tr").data("tt-type");
      var parent_id = $(this).parents("tr").data("tt-parent-id");
      parent_id = typeof parent_id == 'undefined' ? 0 : parent_id;
      $('#share-form input[name="id"]').val(id);
      $('#ShareModal input[name="parent_id"]').val(parent_id);
      $('#related-form input[name="id"]').val(id);

      if(type == "file"){
        var parent_id = $(this).parents('tr').data('tt-parent-id');
        var id_set = $(this).parents('tr').data('tt-id');
        id = '';
      }
      $('#AddFolderModal input[name="parent_id"]').val(id);
    })

    $(document).on("dblclick","#spreadsheet-advanced .tr-pointer",function() {
        var type = $(this).parents("tr").data("tt-type");
        var parent_id = $(this).parents('tr').data('tt-parent-id');
        parent_id = parent_id == undefined ? 0 : parent_id;
        var id_set = $(this).parents('tr').data('tt-id');
        var name = $(this).parents('tr').data('tt-name');
        var share = $('.button-group__mono-colors').data('share');
        share = share == undefined ? "related" : share;

        if(type == "file"){
          if(share == false){
            window.location.replace(admin_url + 'spreadsheet_online/new_file_view/'+parent_id+'/'+id_set);
          }else if(share == true){
            requestGet(admin_url + 'spreadsheet_online/get_hash_staff/' + id_set).done(function(response) {
              response = JSON.parse(response);
              window.location.replace(admin_url + 'spreadsheet_online/file_view_share/'+response.hash);
            })
          }else if(share == "related"){
            var pathname = window.location.pathname.split('/')[2];
            switch (pathname){
              case "invoices" :
              pathname = "invoice";
              var related_id = $('input[name="invoiceid"]').val();
              break;
              case "leads" :
              pathname = "lead";
              var related_id = $('input[name="rel_id"]').val();
              break;
              case "expenses" :
              pathname = "expense";
              var related_id = $('input[name="expenseid"]').val();
              break;
              case "estimates" :
              pathname = "estimate";
              var related_id = $('input[name="estimateid"]').val();
              break;
              case "proposals" :
              pathname = "proposal";
              var related_id = $('input[name="proposal_id"]').val();
              break;
              case "projects" :
              pathname = "project";
              var related_id = $('input[name="project_id"]').val();
              break;
            }
            requestGet(admin_url + 'spreadsheet_online/get_hash_related/' + related_id + '/' + pathname + '/' + id_set).done(function(response) {
              response = JSON.parse(response);
              window.location.replace(admin_url + 'spreadsheet_online/file_view_share_related/'+response.hash);
            })
          }
        }else{
          $('#AddFolderModal input[name="name"]').val(name);
          $('#AddFolderModal input[name="id"]').val(id_set);
          $('#AddFolderModal').modal('show');
        }
    });

    $('.add_file_button').on('click', function(){
      var parent_id = $("input[name='parent_id']").val() == "" ? 0 : $("input[name='parent_id']").val();
      window.location.replace(admin_url + 'spreadsheet_online/new_file_view/'+parent_id);
    })


    
    $(document).mouseup(function(e) 
    {
      if (e.which == 1) {
        var container = $(".context-menu");
        if (!container.is(e.target)) 
        {
          container.removeClass("context-menu--active");
          container.attr("style", "left: 1109px; top: 188px;");
        }
      }

    });


    var _rel_id = $('select[name="rel_id[0]'),
    _rel_type = $('select[name="rel_type[0]'),
    _rel_id_wrapper = $('#rel_id_wrapper');

    $('select[name="rel_type[0]"]').on('change', function() {
      var name = $(this).val();
      requestGet(admin_url + 'spreadsheet_online/get_related/' + _rel_type.val()).done(function(response) {
        response = JSON.parse(response);
        $('[for="rel_id[0]"]').html(name);

        if(response == ''){
          $('select[name="rel_id[0]"]').append('<option value="" selected></option>');
          $('select[name="rel_id[0]"]').selectpicker('refresh');
        }else{
          $('select[name="rel_id[0]"]').html(response);
          $('select[name="rel_id[0]"]').selectpicker('refresh');
        }
      });

    })




    $(document).ready(function() {
      $('.share-status').on('click', function(){
        var parent_id = $(this).parents('tr').data('tt-parent-id');
        parent_id = parent_id == undefined ? 0 : parent_id;
        var id_set = $(this).parents('tr').data('tt-id');
        var type = $(this).parents('tr').data('tt-type');
        var name = $(this).parents('tr').data('tt-name');

        $('#sharedetailModal').modal('show');
        requestGet(admin_url + 'spreadsheet_online/get_share_staff_client/'+id_set).done(function(response) {
          response = JSON.parse(response);
          $('#tab-1 tbody').html(response.staffs);
          $('#tab-2 tbody').html(response.clients);
          $('ul.tabs li').click(function(){
            var tab_id = $(this).attr('data-tab');
            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');
            $(this).addClass('current');
            $("#"+tab_id).addClass('current');
          })
        })
      })

      //related
      $("body").on('click', '.new_box_information_review_related', function() {
        var addMoreBoxInformationReviewInputKey = $('.list_information_fields_review_related select[name*="rel_type"]').length;

        if ($(this).hasClass('disabled')) { return false; }

        var newattachment = $('.list_information_fields_review_related').find('#item_information_fields_review_related').eq(0).clone().appendTo('.list_information_fields_review_related');
        newattachment.find('button[role="combobox"]').remove();

        newattachment.find('[for="rel_type[0]"]').attr('for', 'rel_type[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('[app-field-wrapper="rel_type[0]"]').attr('app-field-wrapper', 'rel_type[' + addMoreBoxInformationReviewInputKey + ']');;
        newattachment.find('select[name="rel_type[0]"]').attr('id', 'rel_type[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('select[name="rel_type[0]"]').attr('name', 'rel_type[' + addMoreBoxInformationReviewInputKey + ']');

        newattachment.find('[app-field-wrapper="rel_id[0]"]').attr('app-field-wrapper', 'rel_id[' + addMoreBoxInformationReviewInputKey + ']');;
        newattachment.find('[for="rel_id[0]"]').attr('for', 'rel_id[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('select[name="rel_id[0]"]').attr('id', 'rel_id[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('select[name="rel_id[0]"]').attr('name', 'rel_id[' + addMoreBoxInformationReviewInputKey + ']');


        newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button[name="add"]').removeClass('new_box_information_review_related').addClass('remove_box_information_review_related').removeClass('btn-success').addClass('btn-danger');
        $('select[name="rel_type['+ addMoreBoxInformationReviewInputKey +']').selectpicker('refresh');
        $('select[name="rel_id['+ addMoreBoxInformationReviewInputKey +']').selectpicker('refresh');

        $('select[name="rel_type['+ addMoreBoxInformationReviewInputKey +']').val('');
        $('select[name="rel_type['+ addMoreBoxInformationReviewInputKey +']').selectpicker('refresh');
        $('select[name="rel_id['+ addMoreBoxInformationReviewInputKey +']').html('');
        $('select[name="rel_id['+ addMoreBoxInformationReviewInputKey +']').val('');
        $('[for="rel_id['+ (addMoreBoxInformationReviewInputKey) +']"]').html('value');

        $('select[name="rel_id['+ addMoreBoxInformationReviewInputKey +']').selectpicker('refresh');

        var _rel_id = $('select[name="rel_id['+ addMoreBoxInformationReviewInputKey +']'),
        _rel_type = $('select[name="rel_type['+ addMoreBoxInformationReviewInputKey +']'),
        _rel_id_wrapper = $('#rel_id_wrapper');

        $('select[name="rel_type['+ addMoreBoxInformationReviewInputKey +']"]').on('change', function() {
          var name = $(this).val();
          requestGet(admin_url + 'spreadsheet_online/get_related/' + _rel_type.val()).done(function(response) {
            response = JSON.parse(response);
            $('[for="rel_id['+ (addMoreBoxInformationReviewInputKey-1) +']"]').html(name);
            if(response == ''){
              $('select[name="rel_id['+ (addMoreBoxInformationReviewInputKey-1) +']"]').append('<option value=""></option>');
            }else{
              $('select[name="rel_id['+ (addMoreBoxInformationReviewInputKey-1) +']"]').append(response);
              $('select[name="rel_id['+ (addMoreBoxInformationReviewInputKey-1) +']"]').selectpicker('refresh');
            }
          })
        });
        addMoreBoxInformationReviewInputKey++;
      });

      $("body").on('click', '.remove_box_information_review_related', function() {
        $(this).parents('#item_information_fields_review_related').remove();
      });


      $("body").on('click', '.new_box_information_review', function() {
        var addMoreBoxInformationReviewInputKey = $('.list_information_fields_review select[name*="departments_share"]').length;
        if ($(this).hasClass('disabled')) { return false; }

        var newattachment = $('.list_information_fields_review').find('#item_information_fields_review').eq(0).clone().appendTo('.list_information_fields_review');
        newattachment.find('button[role="combobox"]').remove();

        newattachment.find('button[data-id="departments_share[0]"]').attr('data-id', 'departments_share[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('[for="departments_share[0]"]').remove();
        newattachment.find('select[name="departments_share[0]"]').attr('id', 'departments_share[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('select[name="departments_share[0]"]').attr('name', 'departments_share[' + addMoreBoxInformationReviewInputKey + ']');


        newattachment.find('button[data-id="staffs_share[0]"]').attr('data-id', 'staffs_share[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('label[for="staffs_share[0]"]').remove();
        newattachment.find('select[name="staffs_share[0]"]').attr('id', 'staffs_share[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('select[name="staffs_share[0]"]').attr('name', 'staffs_share[' + addMoreBoxInformationReviewInputKey + ']');


        newattachment.find('button[data-id="role_staff[0]"]').attr('data-id', 'role_staff[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('label[for="role_staff[0]"]').remove();
        newattachment.find('select[name="role_staff[0]"]').attr('id', 'role_staff[' + addMoreBoxInformationReviewInputKey + ']');
        newattachment.find('select[name="role_staff[0]"]').attr('name', 'role_staff[' + addMoreBoxInformationReviewInputKey + ']');

        newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button[name="add"]').removeClass('new_box_information_review').addClass('remove_box_information_review').removeClass('btn-success').addClass('btn-danger');

        $('select[name="staffs_share['+ addMoreBoxInformationReviewInputKey +']').selectpicker('refresh');
        $('select[name="departments_share['+ addMoreBoxInformationReviewInputKey +']').selectpicker('refresh');
        $('select[name="role_staff['+ addMoreBoxInformationReviewInputKey +']').selectpicker('refresh');
        if(staff_share_value != ''){
          onchane_handle_department(addMoreBoxInformationReviewInputKey, staff_share_value);
        }
        addMoreBoxInformationReviewInputKey++;
      });

      $("body").on('click', '.remove_box_information_review', function() {
        $(this).parents('#item_information_fields_review').remove();
      });

      $("body").on('click', '.new_box_information_review_client', function() {
        var addMoreBoxInformationReviewInputKeyClient = $('.list_information_fields_review_client select[name*="client_groups_share"]').length;
        if ($(this).hasClass('disabled')) { return false; }

        var newattachment = $('.list_information_fields_review_client').find('#item_information_fields_review_client').eq(0).clone().appendTo('.list_information_fields_review_client');
        newattachment.find('button[role="combobox"]').remove();

        newattachment.find('button[data-id="client_groups_share[0]"]').attr('data-id', 'client_groups_share[' + addMoreBoxInformationReviewInputKeyClient + ']');
        newattachment.find('label[for="client_groups_share[0]"]').remove();
        newattachment.find('select[name="client_groups_share[0]"]').attr('id', 'client_groups_share[' + addMoreBoxInformationReviewInputKeyClient + ']');
        newattachment.find('select[name="client_groups_share[0]"]').attr('name', 'client_groups_share[' + addMoreBoxInformationReviewInputKeyClient + ']');


        newattachment.find('button[data-id="clients_share[0]"]').attr('data-id', 'clients_share[' + addMoreBoxInformationReviewInputKeyClient + ']');
        newattachment.find('label[for="clients_share[0]"]').remove();
        newattachment.find('select[name="clients_share[0]"]').attr('id', 'clients_share[' + addMoreBoxInformationReviewInputKeyClient + ']');
        newattachment.find('select[name="clients_share[0]"]').attr('name', 'clients_share[' + addMoreBoxInformationReviewInputKeyClient + ']');


        newattachment.find('button[data-id="role_client[0]"]').attr('data-id', 'role_client[' + addMoreBoxInformationReviewInputKeyClient + ']');
        newattachment.find('label[for="role_client[0]"]').remove();
        newattachment.find('select[name="role_client[0]"]').attr('id', 'role_client[' + addMoreBoxInformationReviewInputKeyClient + ']');
        newattachment.find('select[name="role_client[0]"]').attr('name', 'role_client[' + addMoreBoxInformationReviewInputKeyClient + ']');

        newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
        newattachment.find('button[name="add"]').removeClass('new_box_information_review_client').addClass('remove_box_information_review_client').removeClass('btn-success').addClass('btn-danger');

        $('select[name="client_groups_share['+ addMoreBoxInformationReviewInputKeyClient +']').selectpicker('refresh');
        $('select[name="clients_share['+ addMoreBoxInformationReviewInputKeyClient +']').selectpicker('refresh');
        $('select[name="role_client['+ addMoreBoxInformationReviewInputKeyClient +']').selectpicker('refresh');
        if(client_share_value != ''){
          onchane_handle_client(addMoreBoxInformationReviewInputKeyClient, client_share_value);
        }
        addMoreBoxInformationReviewInputKeyClient++;
      });
      $("body").on('click', '.remove_box_information_review_client', function() {
        $(this).parents('#item_information_fields_review_client').remove();
      });
    }); 

    $('.related-to-hanlde').click(function(){
      var main = $(this).data('data-main');
      $('.content-related').html('<li>'+main+'</li>');
      $('#relateDetailModal').modal('show');
    })

    $('.add_related_button').on('click', function(){
      var val = $('#RelatedModal [name="id"]').val();
      if(val != ''){
        requestGet(admin_url + 'spreadsheet_online/get_related_id/'+ val).done(function(response) {
          response = JSON.parse(response);
          $.each(response.type, function(index, value){
            if(index > 0){
              $('.new_box_information_review_related').click();
              $('select[name="rel_type['+index+']"]').val(value).change();
              requestGet(admin_url + 'spreadsheet_online/get_related/' + value).done(function(response) {
                response = JSON.parse(response);
                $('[for="rel_id['+ index +']"]').html(value);
                if(response == ''){
                  $('select[name="rel_id['+ index +']"]').html('');
                  $('select[name="rel_id['+ index +']"]').append('<option value=""></option>');
                }else{
                  $('select[name="rel_id['+ index +']"]').html('');
                  $('select[name="rel_id['+ index +']"]').append(response);
                  $('select[name="rel_id['+ index +']"]').selectpicker('refresh');
                }
              })
            }else{
              $('select[name="rel_type['+index+']"]').val(value).change();
              requestGet(admin_url + 'spreadsheet_online/get_related/' + value).done(function(response) {
                response = JSON.parse(response);
                $('[for="rel_id['+ index +']"]').html(value);
                if(response == ''){
                  $('select[name="rel_id['+ index +']"]').html('');
                  $('select[name="rel_id['+ index +']"]').append('<option value=""></option>');
                }else{
                  $('select[name="rel_id['+ index +']"]').html('');
                  $('select[name="rel_id['+ index +']"]').append(response);
                  $('select[name="rel_id['+ index +']"]').selectpicker('refresh');
                }
              })

            }

          })
          
        })
      }
    })
    $(document).ready(function(){
        $('input[name="group_share_staff"]').click(function(){
            if($(this).prop("checked") == true){
                $('.choosee-staff').removeClass('hide');
            }
            else if($(this).prop("checked") == false){
                $('.choosee-staff').addClass('hide');

            }
        });

        $('input[name="group_share_client"]').click(function(){
            if($(this).prop("checked") == true){
                $('.choosee-customer').removeClass('hide');
            }
            else if($(this).prop("checked") == false){
                $('.choosee-customer').addClass('hide');
            }
        });
    });

    $('.setting-sent-notifications').click(function(){
      $('#setting-sent-notifications').modal('show');
    })


  })(jQuery);

  function new_folder(){
    "use strict";
    if($('#name').val() != ''){
      $.post(admin_url + 'projects/folder/' + project_id, {
        name: $('#name').val(),
        parent:$('#folder_file_id').val()
      }).done(function() {
        $('.table-project_file').DataTable().destroy();
        if($('#folder_file_id').val() != '' && $('#folder_file_id').val() > 0){
          initDataTable('.table-project_file', admin_url+'projects/project_files_table/'+project_id+'/'+$('#folder_file_id').val());
        }else{
          initDataTable('.table-project_file', admin_url+'projects/project_files_table/'+project_id);
        }
      })
      $('#folder').modal('hide');
    }
    else{alert('Folder name is null!')}
  };




function onchane_handle_department(index, staff_share){
   "use strict";
 $('select[name="departments_share['+index+']"]').on('change', function(){
  var val = $(this).val();
  var name = $(this).attr("name");
  if(val != ''){
    requestGet(admin_url + 'spreadsheet_online/append_value_department/'+ val).done(function(response) {
      response = JSON.parse(response);
      $('select[name="staffs_share['+index+']"]').html(response);
      $('select[name="staffs_share['+index+']"]').selectpicker('refresh');
      if(staff_share != ''){
        $('select[name="staffs_share['+index+']"]').val(staff_share).change();
      }
    })
  }else{
    requestGet(admin_url + 'spreadsheet_online/get_staff_all').done(function(response_staff) {
      response_staff = JSON.parse(response_staff);
      $('select[name="staffs_share['+index+']"]').html(response_staff);
      $('select[name="staffs_share['+index+']"]').selectpicker('refresh');
      if(staff_share != ''){
        $('select[name="staffs_share['+index+']"]').val(staff_share).change();
      }
    })
  }
})
}
function onchane_handle_client(index, client_share){
   "use strict";
 $('select[name="client_groups_share['+index+']"]').on('change', function(){
  var val = $(this).val();
  var name = $(this).attr("name");
  if(val != ''){
    requestGet(admin_url + 'spreadsheet_online/append_value_group/'+ val).done(function(response) {
      response = JSON.parse(response);
      $('select[name="clients_share['+index+']"]').html(response);
      $('select[name="clients_share['+index+']"]').selectpicker('refresh');
      if(client_share != ''){
        $('select[name="clients_share['+index+']"]').val(client_share).change();
      }
    })
  }else{
    requestGet(admin_url + 'spreadsheet_online/get_client_all').done(function(response_client) {
      response_client = JSON.parse(response_client);
      $('select[name="clients_share['+index+']"]').html(response_client);
      $('select[name="clients_share['+index+']"]').selectpicker('refresh');
      if(client_share != ''){
        $('select[name="clients_share['+index+']"]').val(client_share).change();
      }
    })
  }
})
}

function handle_click_checkbox(){
   "use strict";
  $('input[type="checkbox"]').click(function(){
    var val = $(this).val();
    var hide_old = $('input[name="value-hidden"]').val();
    if(hide_old.length > 3){
      $('input[name="value-hidden"]').val('');
    }
    if(hide_old.length == 3){
      if(val == 1){
        $('input[name="value-hidden"]').val(2);
      }else{
        $('input[name="value-hidden"]').val(1);
      }
    }else if(hide_old.length == 1){
      if(val == hide_old){
        $('input[name="value-hidden"]').val("");
      }else{
        hide_old != '' ? $('input[name="value-hidden"]').val(hide_old+ ',' +val) : $('input[name="value-hidden"]').val(val);
      }
    }else if(hide_old == ''){
      $('input[name="value-hidden"]').val(val);
    }
    var hide = $('input[name="value-hidden"]').val();
    $('.share-row').removeClass('hide');
    if(hide == "1"){
      $('.choosee-staff').removeClass('hide');
      $('.choosee-customer').addClass('hide');
      $('select[name*="clients_share"]').val('');
      $('select[name*="client_groups_share"]').val('');
      $('select[name*="role_staff"]').val(1).change();
      $('select[name*="role_staff"]').selectpicker('refresh');
      $('select[name*="client_groups_share"]').selectpicker('refresh');
      $('select[name*="clients_share"]').selectpicker('refresh');
    }else if(hide == "2"){
      $('.choosee-staff').addClass('hide');
      $('.choosee-customer').removeClass('hide');
      $('select[name*="staffs_share"]').val('');
      $('select[name*="departments_share"]').val('');
      $('select[name*="role_client"]').val(1).change();
      $('select[name*="role_client"]').selectpicker('refresh');
      $('select[name*="departments_share"]').selectpicker('refresh');
      $('select[name*="staffs_share"]').selectpicker('refresh');
    }else if(hide == "1,2" || hide == "2,1"){
      $('.choosee-staff').removeClass('hide');
      $('.choosee-customer').removeClass('hide');
    }else{
      $('.choosee-staff').addClass('hide');
      $('.choosee-customer').addClass('hide');
    }
  })
}

(function () {
  "use strict";
  var jQueryPlugin = (window.jQueryPlugin = function (ident, func) {
    return function (arg) {
      if (this.length > 1) {
        this.each(function () {
          var $this = $(this);

          if (!$this.data(ident)) {
            $this.data(ident, func($this, arg));
          }
        });

        return this;
      } else if (this.length === 1) {
        if (!this.data(ident)) {
          this.data(ident, func(this, arg));
        }

        return this.data(ident);
      }
    };
  });
})();

(function () {
  "use strict";
  var TooltipStyle =
    "<style id='aks-tooltip-style'>[data-tooltip]{position:relative;display:inline-block}[data-tooltip] .aks-tooltip{position:absolute;width:fit-content;min-width:fit-content;padding:6px 10px;border-radius:5px;box-shadow:0 1em 2em -.5em rgba(0,0,0,.35);background:#020204;opacity:0;color:#fff;font-size:13px;font-weight:400;text-align:center;text-transform:none;line-height:1;user-select:none;pointer-events:none;visibility:hidden;z-index:1}[data-tooltip] .aks-tooltip::after{display:inline-block;position:absolute;content:''}[data-tooltip-location=up] .aks-tooltip{bottom:calc(100% + 10px);left:50%;transform:translateX(-50%);animation:tooltips-vert .3s ease-out forwards}[data-tooltip-location=up] .aks-tooltip::after{bottom:-4px;left:50%;transform:translateX(-50%);border-top:5px solid #020204;border-right:5px solid transparent;border-left:5px solid transparent}[data-tooltip-location=down] .aks-tooltip{top:calc(100% + 10px);left:50%;transform:translateX(-50%);animation:tooltips-vert .3s ease-out forwards}[data-tooltip-location=down] .aks-tooltip::after{top:-4px;left:50%;transform:translateX(-50%);border-right:5px solid transparent;border-bottom:5px solid #020204;border-left:5px solid transparent}[data-tooltip-location=left] .aks-tooltip{top:50%;right:calc(100% + 10px);transform:translateY(-50%);animation:tooltips-horz .3s ease-out forwards}[data-tooltip-location=left] .aks-tooltip::after{top:50%;right:-4px;transform:translateY(-50%);border-top:5px solid transparent;border-bottom:5px solid transparent;border-left:5px solid #020204}[data-tooltip-location=right] .aks-tooltip{top:50%;left:calc(100% + 10px);transform:translateY(-50%);animation:tooltips-horz .3s ease-out forwards}[data-tooltip-location=right] .aks-tooltip::after{top:50%;left:-4px;transform:translateY(-50%);border-top:5px solid transparent;border-right:5px solid #020204;border-bottom:5px solid transparent}@-moz-keyframes tooltips-vert{to{opacity:.9;transform:translate(-50%,0)}}@-webkit-keyframes tooltips-vert{to{opacity:.9;transform:translate(-50%,0)}}@-o-keyframes tooltips-vert{to{opacity:.9;transform:translate(-50%,0)}}@keyframes tooltips-vert{to{opacity:.9;transform:translate(-50%,0)}}@-moz-keyframes tooltips-horz{to{opacity:.9;transform:translate(0,-50%)}}@-webkit-keyframes tooltips-horz{to{opacity:.9;transform:translate(0,-50%)}}@-o-keyframes tooltips-horz{to{opacity:.9;transform:translate(0,-50%)}}@keyframes tooltips-horz{to{opacity:.9;transform:translate(0,-50%)}}</style>";
  $("head").append(TooltipStyle);

  function Tooltip($root) {
    const element = $root;
    const tooltip_el = $root.first("[data-tooltip]");
    const tooltip = $root.data("tooltip");
    element.append('<span class="aks-tooltip">' + tooltip + "</span>");
    const tooltip_container = $root.find(".aks-tooltip");

    tooltip_el.mousemove(function (event) {
      tooltip_container.css({ opacity: "1", visibility: "visible" });
    });
    tooltip_el.mouseout(function (event) {
      tooltip_container.css({ opacity: "0", visibility: "hidden" });
    });
  }

  $.fn.Tooltip = jQueryPlugin("Tooltip", Tooltip);
  $("[data-tooltip]").Tooltip();
})();
