function show_more_job(argument) {
	"use strict";
	var disbled_button = $('#disbled_button').find('.disabled').html();

	if(!disbled_button){
		var search_ = $('input[name="search"]').val();
		var page_ = $('input[name="current_page"]').val();
		var csrf_token_name = $('input[name="csrf_token_name"]').val();

		var data ={};
		data.search = search_;
		data.page = page_;
		data.csrf_token_name = csrf_token_name;

		 $.post(site_url + 'recruitment/recruitment_portal/show_more_job', data).done(function(response) {
	        response = JSON.parse(response); 
	        if(response.status == 'true' || response.status == true){
	        	$('#panel_body_job').append(response.data); 
	        	$('#additional').html('');
	        	$('#additional').append(hidden_input('current_page',response.page));
	        }else{
	        	$('.recruitment_showmore ').addClass(' disabled');
	        } 
	    });
	}
}

//live search job

$(".kb-search-input").keyup(function(){

	var search = $('input[name="search"]').val();
	var page = $('input[name="current_page"]').val();
	var csrf_token_name = $('input[name="csrf_token_name"]').val();

	var data ={};
	data.search = search;
	data.page = page;
	data.csrf_token_name = csrf_token_name;

	$.post(site_url + 'recruitment/recruitment_portal/job_live_search', data).done(function(response) {
	        response = JSON.parse(response); 
	        if(response.status == 'true' || response.status == true){
	        	$('#panel_body_job').html(''); 
	        	$('#panel_body_job').append(response.data); 

	        	$('#additional').html('');
	        	$('#additional').append(hidden_input('current_page',response.page));

	        	$('.title-search').html('');
	        	$('.title-search').html(response.rec_campaingn_total+' Jobs for you');
	        	$('.recruitment_showmore ').removeClass(' disabled');


	        }else{

	        	$('#panel_body_job').html(''); 
	        	$('#panel_body_job').append(response.data); 

	        	$('#additional').html('');
	        	$('#additional').append(hidden_input('current_page',response.page));

	        	$('.title-search').removeClass('hide');
	        	$('.title-search').html('');
	        	$('.title-search').html(response.rec_campaingn_total+' Jobs for you');

	        	$('.recruitment_showmore ').addClass(' disabled');
	        } 
	    });

});
