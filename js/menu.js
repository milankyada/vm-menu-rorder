 jQuery(document).ready(function(){
	
	jQuery(function($) {
	    $( "#sortable1, #sortable2" ).sortable({
	      connectWith: ".connectedSortable"
	    }).disableSelection();
	});
	
	/*** For user roles ***/
	jQuery(function($) {
	    $( "#user_opt, #user_selected_opt" ).sortable({
	      connectWith: ".connectedSortable_user"
	    }).disableSelection();
	});

	jQuery( "#reordered_sortable" ).sortable();
    jQuery( "#reordered_sortable" ).disableSelection();

	var selected_order = [];
	var ajaxURL = jQuery('.ajax-url').val();
	var re_order = jQuery('.re-order-nonce').val();


/*** Set menu order for all ***/
	jQuery('.set_rm').click(function(){
	 	
				jQuery('#sortable2 li').each(function(){

					selected_order.push(jQuery(this).attr('data-page'));
					
			});
				if(selected_order.length>0)
				{
					jQuery.ajax({
									url:ajaxURL,
									typt:'POST',
									data:"action=set_menuorder&order="+selected_order+'&_nonce='+re_order,
									success:function(data){
										if(data)
										{
											selected_order.length=0;
											jQuery('.resp_msg').removeAttr('style');
											jQuery('.resp_msg h3').text('Successfully changed!');
											jQuery('.resp_msg').removeClass('w3-red').addClass('w3-green');
											setTimeout(function(){
												jQuery('.resp_msg').fadeOut(2000);
												window.open(window.location.href,'_self');	
												location.reload();
											},2000);
											
										}
										else
										{
												jQuery('.resp_msg h3').text('Sorry!');
												jQuery('.resp_msg').removeAttr('style');
												jQuery('.resp_msg').removeClass('w3-green').addClass('w3-red');
												jQuery('.resp_msg').fadeOut(2000);
											setTimeout(function(){
												

											},2000);
										}
									}
								});
				}
				else
				{
					alert('Please drop menu in right box');
				}
			});	

/*** Fetch reordered menu ***/
	jQuery('.reord_current_menu').click(function(){
	 	
				jQuery('#reordered_sortable li').each(function(){

					selected_order.push(jQuery(this).attr('data-page'));
					
				});
				if(selected_order.length>0)
				{
					jQuery.ajax({
									url:ajaxURL,
									typt:'POST',
									data:"action=set_menuorder&order="+selected_order+'&_nonce='+re_order,
									success:function(data){
										if(data)
										{
											selected_order.length=0;
											jQuery('.resp_msg').removeAttr('style');
											jQuery('.resp_msg h3').text('Successfully changed!');
											jQuery('.resp_msg').removeClass('w3-red').addClass('w3-green');
											setTimeout(function(){
												jQuery('.resp_msg').fadeOut(2000);
												window.open(window.location.href,'_self');	
												location.reload();
											},2000);
										}
										else
										{
											jQuery('.resp_msg h3').text('Sorry!');
												jQuery('.resp_msg').removeAttr('style');
												jQuery('.resp_msg').removeClass('w3-green').addClass('w3-red');
												jQuery('.resp_msg').fadeOut(2000);
											setTimeout(function(){
												

											},2000);
										}
									}
								});
				}
				else
				{
					jQuery('.resp_msg h3').text('Sorry!');
												jQuery('.resp_msg').removeAttr('style');
												jQuery('.resp_msg').removeClass('w3-green').addClass('w3-red');
												jQuery('.resp_msg').fadeOut(2000);
											setTimeout(function(){
												

											},2000);
				}
			});


/*** When user role change ***/
	jQuery('.user_role').change(function(){
		var user_role = jQuery('.user_role :selected').val();
		jQuery.ajax({
						url:ajaxURL,
						typt:'POST',
						data:"action=get_menuorder_by_user&userrole="+user_role+'&_nonce='+re_order,
						success:function(data){
							if(data)
							{
								var res = jQuery.parseJSON(data);
								console.log(res);
								if(typeof res.right!=="undefined" || typeof res.left!=="undefined")
								{
									if(typeof res.right!=="undefined")
									{
										jQuery('#user_selected_opt').html("");
										jQuery.each(res.right,function(index,value){
											
											jQuery('#user_selected_opt').append('<li class="w3-btn w3-hover-green w3-card-4" data-page="'+index+'">'+value+'</li>');

										})		
									}
									if(typeof res.left!=="undefined")
									{
										jQuery('#user_opt').html("");
										jQuery.each(res.left,function(index,value){
											
											jQuery('#user_opt').append('<li class="w3-btn w3-hover-blue w3-card-4" data-page="'+index+'">'+value+'</li>');

										})	
									}
								}
								else
								{
									jQuery('#user_opt').html("");
									jQuery('#user_selected_opt').html("");
									jQuery.each(res.default_menu,function(index,value){
										
										jQuery('#user_opt').append('<li class="w3-btn w3-hover-blue w3-card-4" data-page="'+index+'">'+value+'</li>');

									})	
								}
								
							}
							else
							{
								alert('Sorry some error occured');
							}
						}
					});
	});



	jQuery('.set_rm_for_user').click(function(){
	 			
	 			var user_role = jQuery('.user_role :selected').val();
				
				jQuery('#user_selected_opt li').each(function(){

					selected_order.push(jQuery(this).attr('data-page'));
					
				});
				if(selected_order.length>0)
				{
					jQuery.ajax({
									url:ajaxURL,
									typt:'POST',
									data:"action=set_menuorder_by_user_role&order="+selected_order+'&_nonce='+re_order+'&user_role='+user_role+'&status=setuser',
									success:function(data){
										if(data)
										{
											selected_order.length=0;
											jQuery('.resp_msg').removeAttr('style');
											jQuery('.resp_msg h3').text('Successfully changed!');
											jQuery('.resp_msg').removeClass('w3-red').addClass('w3-green');
											setTimeout(function(){
												jQuery('.resp_msg').fadeOut(2000);
												window.open(window.location.href,'_self');	
												location.reload();
											},2000);
										}
										else
										{
											jQuery('.resp_msg h3').text('Sorry!');
												jQuery('.resp_msg').removeAttr('style');
												jQuery('.resp_msg').removeClass('w3-green').addClass('w3-red');
												jQuery('.resp_msg').fadeOut(2000);
											setTimeout(function(){
												

											},2000);
										}
									}
								});
				}
				else
				{
					jQuery('.resp_msg h3').text('Sorry!');
												jQuery('.resp_msg').removeAttr('style');
												jQuery('.resp_msg').removeClass('w3-green').addClass('w3-red');
												jQuery('.resp_msg').fadeOut(2000);
											setTimeout(function(){
												

											},2000);
				}
			});	

	/*** Reset everything ***/
	jQuery('.reset_all_menu').click(function(){
		var reply = confirm("All changes will be deleted. \nAre you sure?");
		if(reply)
		{
			jQuery.ajax({
					url:ajaxURL,
					typt:'POST',
					data:"action=set_to_defaul",
					success:function(data){
						location.reload();
					}
			});
		}
			
	});

	/*** Reset for particular user role ***/
	
	jQuery('.reset_u_role').click(function(){

		var selected_role = jQuery("#reset_role").serialize();
		if(selected_role=="" || typeof selected_role=="undefined")
		{
			jQuery('.resp_msg h3').text('Select atleast one role!');
			jQuery('.resp_msg').removeAttr('style');
			jQuery('.resp_msg').removeClass('w3-green').addClass('w3-red');
			jQuery('.resp_msg').fadeOut(2500);
		}
		else{
			jQuery.ajax({
					url:ajaxURL,
					typt:'POST',
					data:"action=reset_for_userrole&"+selected_role+"&_nonce="+re_order,
					success:function(data){
						if(data)
						{
							jQuery('.resp_msg').removeAttr('style');
							jQuery('.resp_msg h3').text('Successfully changed!');
							jQuery('.resp_msg').removeClass('w3-red').addClass('w3-green');
							setTimeout(function(){
								jQuery('.resp_msg').fadeOut(2000);
								window.open(window.location.href,'_self');	
								location.reload();
							},2000);	
							location.reload();
						}
						else{
							jQuery('.resp_msg h3').text('Some error occured!');
							jQuery('.resp_msg').removeAttr('style');
							jQuery('.resp_msg').removeClass('w3-green').addClass('w3-red');
							jQuery('.resp_msg').fadeOut(2500);
						}
					}
			});
		}
			
	});

	});

	
/*** To open tab ***/
function openCity(evt, cityName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("city");
  for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " w3-red";
}
