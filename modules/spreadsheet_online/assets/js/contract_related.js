(function(){
  "use strict";
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
        var pathname = window.location.pathname.split('/')[1];
        var related_id = window.location.pathname.split('/')[2];

        $.get(admin_url + 'spreadsheet_online/get_hash_related/' + related_id + '/' + pathname + '/' + id_set).done(function(response) {
          response = JSON.parse(response);
          window.location.replace(site_url + 'spreadsheet_online/spreadsheet_online_client/file_view_share_related/'+response.hash);
        })
      }
    }else{
      $('#AddFolderModal input[name="name"]').val(name);
      $('#AddFolderModal input[name="id"]').val(id_set);
      $('#AddFolderModal').modal('show');
    }
  });

})(jQuery);
  