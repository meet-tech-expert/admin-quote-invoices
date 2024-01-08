jQuery(document).ready(function(){
		jQuery( '.admin-date').datepicker({dateFormat : "yy-mm-dd"});	
		
		jQuery("#add_new_product").on('click' ,function(){
			add_new_product();
		});
		jQuery("#add_new_checklist").on('click' ,function(){
			add_new_checklist();
		});
		
		jQuery(".remove-product").on('click' ,function(){
			jQuery(this).closest('tr').remove();
			trigger_table();
		});
		jQuery(".remove-checklist").on('click' ,function(){
			jQuery(this).closest('tr').remove();
		});
		
		jQuery("select#prod_lists").on('change' ,function(){
			if(jQuery(this).val() != ''){
				var name  = jQuery("select#prod_lists option:selected").text();
				var cost = jQuery("select#prod_lists option:selected").data('cost');
				add_new_product(name ,cost);
			}
			
		});	
		
		jQuery(".flags").on('click',function(){
			checkbox_validation();
			validate_this();
 		});
		
		setTimeout(function(){
			//console.log('k');
			trigger_on_change();
			trigger_table();
			checkbox_validation();
		},500);
		
		jQuery(".remove_file").on('click' ,function(){
			var id = jQuery(this).data('id');
			jQuery("#li-"+id).remove();
		});
		
		jQuery("a.delete_record").on('click' ,function(e){
			e.preventDefault(); 
			if(confirm("Sei sicuro di voler cancellare il record?"))
			{ 
				var _href = jQuery(this).attr('href');
				window.location.href = _href;
			}  
		});
		
	
});
function add_new_attach(data){
	var html = '';
	var image_url= '';
	    if(data.type != 'image'){
			 image_url = data.icon;
		}else{
			image_url = data.url;
		}
	    html += '<li class="attachment-li" id="li-'+data.id+'"><div class="attachment-preview-div"><div class="thumbnail-div"><img src="'+image_url+'" alt=""></div><div class="info"><a target="_blank" title="'+data.filename+'" href="'+data.url+'" class="file_name">vista</a></div><div class="desc"><input name="attach_id[]" type="hidden" value="'+data.id+'" /><input placeholder="Descrizione" name="desc[]" type="text"/></div><div class="remove-div"><span class="remove_file" data-id="'+data.id+'" >Rimuovere</span></div></div></li>';
	
	jQuery(".append-attach").append(html);
	jQuery(".remove_file").on('click' ,function(){
		var id = jQuery(this).data('id');
		jQuery("#li-"+id).remove();
	});
}


function add_new_product(name='' ,cost=''){
	var html = '';
	    html += '<tr valign="top"><td><input type="text" name="product[]" value="'+name+'"  required></td><td><input type="number" step="any" class="pro_cost" name="cost[]" value="'+cost+'" min="1" required></td><td><input type="number" step="any" class="pro_qty" name="qty[]" value="" min="1" required></td><td><input type="number" step="any" class="pro_tax" name="tax[]" value="" min="1" required></td><td><span class="ttl_cost">0</span><span title="Rimuovere" class="dashicons dashicons-no remove-product"></span></td></tr>';
	   
	   jQuery("#product_body").append(html); 
	   
	   jQuery(".remove-product").on('click' ,function(){
			jQuery(this).closest('tr').remove();
			trigger_table();
		});
		trigger_table();
		trigger_on_change();
}

function trigger_on_change(){
	jQuery(".product-table input[type='number']").on('change' ,function(){  trigger_table(); })
}

function trigger_table(){
	var final_cost = 0;
	var final_sum = 0;
	var final_tax  = 0;
	var cost_w_qty_tax  = 0;
	jQuery(".pro_cost").each(function(){ 
		var cost = jQuery(this).val(); 
		var qty = jQuery(this).parents('tr').find('.pro_qty').val();
		var cost_w_qty = cost * qty;  
 		
 		if(cost =='undefined' || cost ==''){
			cost = 0;
		}
		var tax = jQuery(this).parents('tr').find('.pro_tax').val();
		if(tax =='undefined' || tax ==''){
			tax = 0;
		}
		cost_w_qty_tax = parseFloat(cost_w_qty)  + parseFloat((cost_w_qty * tax)/100); 
		jQuery(this).parents('tr').find('.ttl_cost').text(cost_w_qty_tax.toFixed(2));
		
 		final_cost = parseFloat(final_cost) + parseFloat(cost);
 		jQuery('.final_cost').text(final_cost.toFixed(2));
 		
 		final_tax = parseFloat(final_tax) + parseFloat(tax);
 		jQuery('.final_tax').text(final_tax.toFixed(2));
 		
 		final_sum = parseFloat(final_sum) + parseFloat(cost_w_qty_tax);
 		jQuery('.final_sum').text(final_sum.toFixed(2));

 	})
}


jQuery(function($){
var frame,metaBox = $('.attach-outer-div');

$("#add_new_attach").on( 'click', function( event ){
    // If the media frame already exists, reopen it.
    if ( frame ) {
      frame.open();
      return;
    }
    // Create a new media frame
    frame = wp.media({
      title: 'Seleziona o carica i media della tua persuasione scelta',
      button: {
        text: 'carica questo allegato'
      },
      multiple: true  // Set to true to allow multiple files to be selected
    });
    
    frame.on( 'select', function() {
      
      var selection = frame.state().get('selection');
      selection.map( function( attachments ) {
        attachments = attachments.toJSON();
        add_new_attach(attachments);
      });
      
    });
    frame.open();
  });
});

function add_new_checklist(){
	var html = '';
	    html += '<tr valign="top"><th scope="row"><label class=""></label></th><td><input type="checkbox" name="checkbox[]" /><input type="text" name="checklist[]" required /> <span title="Rimuovere" class="dashicons dashicons-no remove-checklist"></span></td></tr>';
	   
	   jQuery("#checklist").append(html);
	   
	   jQuery(".remove-checklist").on('click' ,function(){
			jQuery(this).closest('tr').remove();
		});
		checkbox_validation();
		validate_this();
		
		jQuery(".flags").on('click',function(){
			checkbox_validation();
			validate_this();
 		});
	
}
function checkbox_validation(){
	var chk = jQuery('input[name="checkbox[]"]:checked').length; 
 			var all_chk = jQuery('input[name="checkbox[]"]').length;
 			if(all_chk!='0' && chk == all_chk){
				jQuery('input[name="effective_start_date"],input[name="effective_end_date"]').attr('required','required');
			}else{
				jQuery('input[name="effective_start_date"],input[name="effective_end_date"]').removeAttr('required');
			}
}
function validate_this(){
	var eff_end = jQuery('input[name="effective_end_date"]').val();
	if(eff_end != ""){
		jQuery('input[name="checkbox[]"]').prop('required','required');
	}else{
		jQuery('input[name="checkbox[]"]').removeAttr('required');
	}
	checkbox_validation();
}