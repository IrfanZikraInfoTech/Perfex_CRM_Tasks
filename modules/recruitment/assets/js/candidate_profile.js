(function($) {
"use strict";
    
    var ProposalServerParams = {
        "status_filter": "[name='change_status[]']",
        "campaign_filter": "[name='rec_campaign[]']",
    };

    var table_rec_candidate = $('.table-table_rec_candidate');
	initDataTable('.table-table_rec_candidate', admin_url+'recruitment/table_candidates', [0], [0], ProposalServerParams, [0, 'desc']);

         //hide first column
         var hidden_columns = [0];
         $('.table-table_rec_candidate').DataTable().columns(hidden_columns).visible(false, false);

    $.each(ProposalServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {  
            table_rec_candidate.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
    });	

    candidate_profile_kanban();

})(jQuery);
function send_mail_candidate(){
"use strict";
  $('#mail_modal').modal('show');
  appValidateForm($('#mail_candidate-form'), {
           content: 'required', subject:'required',email:'required'});
}

// Updates task when action performed form kan ban area eq status changed.
function candidate_profile_kanban_update(ui, object) {
    if (object === ui.item.parent()[0]) {
        var status = $(ui.item.parent()[0]).data('task-status-id');
        var tasks = $(ui.item.parent()[0]).find('[data-task-id]');

        var data = {};
        data.order = [];
        var i = 0;
        $.each(tasks, function() {
            data.order.push([$(this).data('task-id'), i]);
            i++;
        });

        candidate_change_status(status, $(ui.item).data('task-id'));
        check_kanban_empty_col_candidate('[data-task-id]');
    }
}

// Init tasks kan ban
function candidate_profile_kanban() {
    "use strict";
    recruitment_init_kanban('recruitment/kanban', candidate_profile_kanban_update, '.tasks-status', 265, 360);
}

// General function to init kan ban based on settings
function recruitment_init_kanban(url, callbackUpdate, connect_with, column_px, container_px, callback_after_load) {
    "use strict";
    if ($('#kan-ban').length === 0) { return; }
    var parameters = [];
    var _kanban_param_val;

    $.each($('#kanban-params input'), function() {
        if ($(this).attr('type') == 'checkbox') {
            _kanban_param_val = $(this).prop('checked') === true ? $(this).val() : '';
        } else {
            _kanban_param_val = $(this).val();
        }
        if (_kanban_param_val !== '') {
            parameters[$(this).attr('name')] = _kanban_param_val;
        }
    });

    var search = $('input[name="search"]').val();
    if (typeof(search) != 'undefined' && search !== '') { parameters['search'] = search; }

    var sort_type = $('input[name="sort_type"]');
    var sort = $('input[name="sort"]').val();
    if (sort_type.length != 0 && sort_type.val() !== '') {
        parameters['sort_by'] = sort_type.val();
        parameters['sort'] = sort;
    }

    parameters['kanban'] = true;
    url = admin_url + url;
    url = buildUrl(url, parameters);
    delay(function() {
        $("body").append('<div class="dt-loader"></div>');
        $('#kan-ban').load(url, function() {

            fix_kanban_height(column_px, container_px);
            var scrollingSensitivity = 20,
                scrollingSpeed = 60;

            if (typeof(callback_after_load) != 'undefined') { callback_after_load(); }

            $(".status").sortable({
                connectWith: connect_with,
                helper: 'clone',
                appendTo: '#kan-ban',
                placeholder: "ui-state-highlight-card",
                revert: 'invalid',
                scrollingSensitivity: 50,
                scrollingSpeed: 70,
                sort: function(event, uiHash) {
                    var scrollContainer = uiHash.placeholder[0].parentNode;
                    // Get the scrolling parent container
                    scrollContainer = $(scrollContainer).parents('.kan-ban-content-wrapper')[0];
                    var overflowOffset = $(scrollContainer).offset();
                    if ((overflowOffset.top + scrollContainer.offsetHeight) - event.pageY < scrollingSensitivity) {
                        scrollContainer.scrollTop = scrollContainer.scrollTop + scrollingSpeed;
                    } else if (event.pageY - overflowOffset.top < scrollingSensitivity) {
                        scrollContainer.scrollTop = scrollContainer.scrollTop - scrollingSpeed;
                    }
                    if ((overflowOffset.left + scrollContainer.offsetWidth) - event.pageX < scrollingSensitivity) {
                        scrollContainer.scrollLeft = scrollContainer.scrollLeft + scrollingSpeed;
                    } else if (event.pageX - overflowOffset.left < scrollingSensitivity) {
                        scrollContainer.scrollLeft = scrollContainer.scrollLeft - scrollingSpeed;

                    }
                },
                change: function() {
                    var list = $(this).closest('ul');
                    var KanbanLoadMore = $(list).find('.kanban-load-more');
                    $(list).append($(KanbanLoadMore).detach());
                },
                start: function(event, ui) {
                    $('body').css('overflow', 'hidden');

                    $(ui.helper).addClass('tilt');
                    $(ui.helper).find('.panel-body').css('background', '#fbfbfb');
                    // Start monitoring tilt direction
                    tilt_direction($(ui.helper));
                },
                stop: function(event, ui) {
                    $('body').removeAttr('style');
                    $(ui.helper).removeClass("tilt");
                    // Unbind temporary handlers and excess data
                    $("html").off('mousemove', $(ui.helper).data("move_handler"));
                    $(ui.helper).removeData("move_handler");
                },
                update: function(event, ui) {
                    callbackUpdate(ui, this);
                }
            });

            $('.status').sortable({
                cancel: '.not-sortable'
            });

        });

    }, 200);
}

// Fixes kanban height to be compatible with content and screen height
function fix_kanban_height(col_px, container_px) {
    "use strict";
    // Set the width of the kanban container
    $("body").find('div.dt-loader').remove();
    var kanbanCol = $('.kan-ban-content-wrapper');
    kanbanCol.css('max-height', (window.innerHeight - col_px) + 'px');
    $('.kan-ban-content').css('min-height', (window.innerHeight - col_px) + 'px');
    var kanbanColCount = parseInt(kanbanCol.length);
    $('.container-fluid').css('min-width', (kanbanColCount * container_px) + 'px');
}

// Kanban load more
function candidate_kanban_load_more(status_id, e, url, column_px, container_px) {
    "use strict";
    var LoadMoreParameters = [];
    var search = $('input[name="search"]').val();
    var _kanban_param_val;
    var page = $(e).attr('data-page');
    var total_pages = $('[data-col-status-id="' + status_id + '"]').data('total-pages');
    if (page <= total_pages) {

        var sort_type = $('input[name="sort_type"]');
        var sort = $('input[name="sort"]').val();
        if (sort_type.length != 0 && sort_type.val() !== '') {
            LoadMoreParameters['sort_by'] = sort_type.val();
            LoadMoreParameters['sort'] = sort;
        }

        if (typeof(search) != 'undefined' && search !== '') {
            LoadMoreParameters['search'] = search;
        }

        $.each($('#kanban-params input'), function() {
            if ($(this).attr('type') == 'checkbox') {
                _kanban_param_val = $(this).prop('checked') === true ? $(this).val() : '';
            } else {
                _kanban_param_val = $(this).val();
            }
            if (_kanban_param_val !== '') {
                LoadMoreParameters[$(this).attr('name')] = _kanban_param_val;
            }
        });

        LoadMoreParameters['status'] = status_id;
        LoadMoreParameters['page'] = page;
        LoadMoreParameters['page']++;
        requestGet(buildUrl(admin_url + url, LoadMoreParameters)).done(function(response) {
            page++;
            $('[data-load-status="' + status_id + '"]').before(response);
            $(e).attr('data-page', page);
            fix_kanban_height(column_px, container_px);
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
        if (page >= total_pages - 1) {
            $(e).addClass("disabled");
        }
    }
}


function candidate_change_status(status, task_id, url) {
    "use-strict"
    url = typeof(url) == 'undefined' ? 'recruitment/candidate_change_status/' + status + '/' + task_id : url;
    var taskModalVisible = $('#task-modal').is(':visible');
    url += '?single_task=' + taskModalVisible;
    $("body").append('<div class="dt-loader"></div>');
    requestGetJSON(url).done(function(response) {
        $("body").find('.dt-loader').remove();
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);

        }else{
            alert_float('danger', response.message);
        }
    });
}

// Check if kanban col is empty and perform necessary actions
function check_kanban_empty_col_candidate(selector) {
    "use-strict"
    var statuses = $('[data-col-status-id]');
    $.each(statuses, function(i, obj) {
        var total = $(obj).find(selector).length;
        if (total == 0) {
            $(obj).find('.kanban-empty').removeClass('hide');
            $(obj).find('.kanban-load-more').addClass('hide');
        } else {
            $(obj).find('.kanban-empty').addClass('hide');
        }
    });
}

  function print_candidate_option(invoker) {
  "use strict";
   var data={};
      data.profit_rate_by_purchase_price_sale = invoker.value;

      if(invoker.value == 1){
        $('.display-select-item').removeClass('hide');
      }else if(invoker.value == 0){
        $('.display-select-item').addClass('hide');
      }
  }


/*print barcode*/
  function print_candidate_bulk_actions(){
    "use strict";
    $('.display-select-item').addClass('hide');
    $("#y_opt_1_").prop("checked", true);

    $("#table_commodity_list_print_candidate option:selected").prop("selected", false).change()
    $("table_commodity_list_print_candidate select[id='item_select_print_candidate']").selectpicker('refresh');

    $('#table_commodity_list_print_candidate').modal('show');
  }

  appValidateForm($("body").find('#item_print_candidate'), {
        'item_select_print_candidate[]': 'required',
      }); 