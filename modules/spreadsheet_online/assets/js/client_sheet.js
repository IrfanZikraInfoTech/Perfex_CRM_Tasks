(function(){
  "use strict";

  $("#spreadsheet-advanced").treetable({ expandable: true });
  $("#spreadsheet-advanced tbody").on("mousedown", "tr", function() {
    $(".selected").not(this).removeClass("selected");
    $(this).toggleClass("selected");
  });

	// Drag & Drop spreadsheet Code
	$("#spreadsheet-advanced .file, #spreadsheet-advanced .folder").draggable({
		helper: "clone",
		opacity: .75,
		refreshPositions: true,
		revert: "invalid",
		revertDuration: 300,
		scroll: true
	});

	$("#spreadsheet-advanced .folder").each(function() {
		$(this).parents("#spreadsheet-advanced tr").droppable({
			accept: ".file, .folder",
			drop: function(e, ui) {
				var droppedEl = ui.draggable.parents("tr");
				$("#spreadsheet-advanced").treetable("move", droppedEl.data("ttId"), $(this).data("ttId"));
			},
			hoverClass: "accept",
			over: function(e, ui) {
				var droppedEl = ui.draggable.parents("tr");
				if(this != droppedEl[0] && !$(this).is(".expanded")) {
					$("#spreadsheet-advanced").treetable("expandNode", $(this).data("ttId"));
				}
			}
		});
	});

  $('.three-dot').on('click', function(){
    var parent_id = $(this).parents('tr').data('tt-parent-id');
    parent_id = parent_id == undefined ? 0 : parent_id;
    var id_set = $(this).parents('tr').data('tt-id');
    var type = $(this).parents('tr').data('tt-type');
    var name = $(this).parents('tr').data('tt-name');
    var share = $('.context-menu').data('share');
    var role = $(this).parents('tr').data('tt-role');

    if(parent_id){
      $('.context-menu').attr('data-parent', parent_id);
      $('.context-menu').attr('data-id', id_set);
    }else{
      $('.context-menu').attr('data-id', id_set);
      $('.context-menu').attr('data-parent', 0);
    }

    if(type == "file"){
      $('.context-menu__link[data-action="create_file"]').parent().remove();
      $('.context-menu__link[data-action="create_folder"]').parent().remove();
      $('.context-menu__link[data-action="d_zip"]').parent().remove();
      if(share == true){
        if(role == 1){
          $('.context-menu__link[data-action="edit"]').parent().remove();
        }else{

          var length_edit = $('.context-menu__link[data-action="edit"]').length;
          length_edit == 0 
          ? 
          (

            $('.context-menu__items').append(`<li class="context-menu__item">
              <a href="#" class="context-menu__link" data-action="edit"><i class="fa fa-empire"></i> Edit </a>
              </li>`)
            )
          : (
            $('.context-menu__link[data-action="edit"]').parent().remove(), 
            $('.context-menu__items').append(`
             <li class="context-menu__item">
             <a href="#" class="context-menu__link" data-action="edit"><i class="fa fa-empire"></i> Edit </a>
             </li>`))

        }
      }
    }else{
      var length = $('.context-menu__link[data-action="create_file"]').length; 
      if(share == false){
        length == 0 ?
        ($('.context-menu__items').append('<li class="context-menu__item"><a href="#" class="context-menu__link" data-action="create_file"><i class="fa fa-ioxhost"></i> create_file</a>'),
          $('.context-menu__items').append('<li class="context-menu__item"><a href="#" class="context-menu__link" data-action="create_folder"><i class="fa fa-ioxhost"></i> create_folder</a>'))
        : 
        ($('.context-menu__link[data-action="create_file"]').remove(),
          $('.context-menu__link[data-action="create_folder"]').remove(),
          $('.context-menu__link[data-action="d_zip"]').remove(),
          $('.context-menu__items').append('<li class="context-menu__item"><a href="#" class="context-menu__link" data-action="create_file"><i class="fa fa-ioxhost"></i> create_file</a>'),
          $('.context-menu__items').append('<li class="context-menu__item"><a href="#" class="context-menu__link" data-action="create_folder"><i class="fa fa-ioxhost"></i> create_folder</a>'))
      }else{

        if(role == 1){
          $('.context-menu__items[data-action="edit"]').remove();
        }else{
          var length_edit = $('.context-menu__link[data-action="edit"]').length;

          length_edit == 0 
          ? 
          (
            $('.context-menu__items').append(`<li class="context-menu__item">
              <a href="#" class="context-menu__link" data-action="edit"><i class="fa fa-empire"></i> Edit </a>
              </li>`)
            )
          : (
            $('.context-menu__link[data-action="edit"]').parent().remove(), 
            $('.context-menu__items').append(`
              <li class="context-menu__item">
              <a href="#" class="context-menu__link" data-action="edit"><i class="fa fa-empire"></i> Edit </a>
              </li>`))
        }
      }
    }


    $(".context-menu").addClass("context-menu--active");

  })


  $(document).mouseup(function(e) 
  {
    if (e.which == 1) {
      var container = $(".context-menu");
      if (!container.is(e.target)) 
      {
        container.removeClass("context-menu--active");
        container.attr("style", "left: 701px; top: 119px;");
      }
    }

  });

  $('#spreadsheet-advanced .tr-pointer').on('dblclick', function(){
      var type = $(this).parents("tr").data("tt-type");
      var parent_id = $(this).parents('tr').data('tt-parent-id');
      parent_id = parent_id == undefined ? 0 : parent_id;
      var id_set = $(this).parents('tr').data('tt-id');
      var name = $(this).parents('tr').data('tt-name');
      var share = $('.button-group__mono-colors').data('share');

      if(type == "file"){
        if(share == false){
          window.location.replace(admin_url + 'spreadsheet_online/new_file_view/'+parent_id+'/'+id_set);
        }else{
          $.get(site_url + 'spreadsheet_online/spreadsheet_online_client/get_hash_client/' + id_set).done(function(response) {
            response = JSON.parse(response);
            window.location.replace(site_url + 'spreadsheet_online/spreadsheet_online_client/file_view_share/'+response.hash);
          })
        }
      }else{
        if($(this).parents("tr").hasClass("collapsed")){
          $("#spreadsheet-advanced").treetable("expandNode", $(this).parents("tr").data("ttId"));
        }else{
          $("#spreadsheet-advanced").treetable("collapseNode", $(this).parents("tr").data("ttId"));
        }
      }
    })
})(jQuery);

function clickInsideElement( e, className ) {
  "use strict";
  var el = e.srcElement || e.target;
  if ( el.classList.contains(className) ) {
    return el;
  } else {
    while ( el = el.parentNode ) {
      if ( el.classList && el.classList.contains(className) ) {
        return el;
      }
    }
  }
  return false;
}

function getPosition(e) {
  "use strict";
  var posx = 0, posy = 0;
  if (!e) var e = window.event;
  if (e.pageX || e.pageY) {
    posx = e.pageX;
    posy = e.pageY;
  } else if (e.clientX || e.clientY) {
    posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
    posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
  }
  return {
    x: posx,
    y: posy
  }
}

