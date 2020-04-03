<style media="screen">
.boxsizingBorder {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
</style>
<div class="alert  ">
	<button class="close" data-dismiss="alert"></button>
	Question: Advanced Input Field
</div>

<p>1. Make the Description, Quantity, Unit price field as text at first. When user clicks the text, it changes to input field for use to edit. Refer to the following video.</p>
<p>2. When user clicks the add button at left top of table, it wil auto insert a new row into the table with empty value. Pay attention to the input field name. For example the quantity field
	<?php echo htmlentities('<input name="data[1][quantity]" class="">')?> ,  you have to change the data[1][quantity] to other name such as data[2][quantity] or data["any other not used number"][quantity]
</p>

<div class="alert alert-success">
	<button class="close" data-dismiss="alert"></button>
	The table you start with
</div>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<th>
			<span id="add_item_button" class="btn mini green addbutton add_field_button" onclick="addToObj=false">
				<i class="icon-plus"></i>
			</span>
		</th>
		<th style="width:50%" >Description</th>
		<th style="width:30%" >Quantity</th>
		<th style="width:20%" >Unit Price</th>
	</thead>

	<tbody  class="input_fields_wrap" >
		<tr data-input="1">
			<td><button type="button" name="button" class="btn red remove_field" >x</i></button>	</td>
			<td class="custom-input"><span></span><textarea name="data[1][description]" class="m-wrap  description required boxsizingBorder" rows="2" style="width:100%;display:none"></textarea></td>
			<td class="custom-input" ><span></span><input type="number" min="0" name="data[1][quantity]" class="boxsizingBorder" style="width:100%;height:100%;display:none"></td>
			<td class="custom-input"><span></span><input type="number" min="0" name="data[1][unit_price]"  class="boxsizingBorder" style="width:100%;height:100%;display:none"></td>
		</tr>
	</tbody>

</table>
<p></p>
<div class="alert alert-info ">
	<button class="close" data-dismiss="alert"></button>
	Video Instruction
</div>
<p style="text-align:left;">
	<video width="78%"   controls><source src="/video/q3_2.mov">Your browser does not support the video tag.</video>
	</p>
	<?php $this->start('script_own');?>
	<script>

	$(document).ready(function()
	{
		// Add and Remove Field
		var wrapper    = $(".input_fields_wrap"); //Fields wrapper
		var add_button = $(".add_field_button"); //Add button ID

		var x = 1;
		$(add_button).click(function(e)
		{
			e.preventDefault();
			x++;
			$(wrapper).append(
				'<tr data-input="'+x+'">' +
				'<td><button type="button" name="button" class="btn red remove_field" >x</button>	</td>'+
				'<td class="custom-input"><span></span><textarea name="data['+x+'][description]" class="m-wrap  description required boxsizingBorder" rows="2" style="width:100%;display:none"></textarea></td>'+
				'<td class="custom-input" ><span></span><input type="number" min="0" name="data['+x+'][quantity]" class="boxsizingBorder" style="width:100%;height:100%;display:none"></td>'+
				'<td class="custom-input"><span></span><input type="number" min="0" name="data['+x+'][unit_price]"  class="boxsizingBorder" style="width:100%;height:100%;display:none"></td>'+
				'</tr>'
			);
		});

		// Remove Row
		$(wrapper).on("click",".remove_field", function(e)
		{
			e.preventDefault();
			$(this).closest ('tr').remove ();
			// $('tr').filter('[data-input='+x+']').remove();
			x--;
		})

		$(document).mouseup(function(e)
		{
			// Alwasy sync input and span
			$(".custom-input").each(function()
			{
				var value = $(this).find(':input').val();
				$(this).find('span').html(value);
			});
			$(".custom-input :input").hide();
			$('.custom-input').find('span').show();
		});

		// Toggle input and text field
		$(wrapper).on('click','.custom-input',function ()
		{
			$(this).find('span').hide();
			$(this).find(':input').show();
		});
	});



	</script>
	<?php $this->end();?>
