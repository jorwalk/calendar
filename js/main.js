//
// DOM READY
$(document).ready(function(){
	Service.connect();
});

var Jumbotron = function(){
	
	function render(data){
		$.get('templates/jumbotron.mst', function(template) {
			var rendered = Mustache.render(template, data);
			$('#jumbotron').html(rendered);
		});
	}

	return {
		render:render
	}
}();

var EventForm = function(){
	
	function render(data){
		$.get('templates/event_form.mst', function(template) {
			var rendered = Mustache.render(template, data);
			$('#event-form').html(rendered);
			onSubmit();
		});
	}

	function onSubmit(){
		$( "form" ).on( "submit", function( event ) {
		  
		  event.preventDefault();

		  console.log( $( this ).serialize() );
		  
		  console.log( encodeURIComponent($( this ).serialize()) );
		});
	}

	return {
		create:function(){
			render(null);
			
		},
		update:function(data){
			render(data);
		}
	}
}();

//
// Click Handler


var Service = function(){
		
		function connect(){
			$.get( "php/get_service.php", function( data ) {
				route(data);
			});	
		}
		
		function route(data){
			if(data.status.connected){
				set_tmpl(data);
				Jumbotron.render(data);
			}
			
			if(data.status.connected === "true"){
				tmpl_navbar(data);
				Events.get();
			}
		}

		function set_tmpl(data){
			$.get('templates/connect_disconnect.mst', function(template) {
				var rendered = Mustache.render(template, data);
				$('.navbar-form.navbar-right').html(rendered);
			});
		}

		function tmpl_navbar(data){
			$.get('templates/navbar.mst', function(template) {
				var rendered = Mustache.render(template, data);
				$('.navbar-collapse.collapse').prepend(rendered);
				NavBarEvent();
			});
		}

		function NavBarEvent(){
			$(".event-create").on('click',function(e){
				e.preventDefault();
				EventForm.create();
			});

			$(".event-update").on("click",function(e){
				e.preventDefault();
				var obj = $.parseJSON( '{ "summary": "TEST SUMMARY", "location":"TEST LOCATION","description":"TEST DESCRIPTION" }' );
				EventForm.update(obj);
			});
		}

	return {
		connect:connect
	}
}();

var Events = function(){

	function get(){
		$.get( "php/get_events.php", function( data ) {
			set_tmpl(data);
		});
	}

	function set_tmpl(data){
		$.get('templates/entries_panel.mst', function(template) {
			var rendered = Mustache.render(template, data);
			$('#target').html(rendered);
		});
	}

	return {
		get:get
	}
}();