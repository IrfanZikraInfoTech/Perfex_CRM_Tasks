(function(){
	"use strict";
	$(document).ready(function(){
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
	})
})(jQuery);