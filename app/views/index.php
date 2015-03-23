<!DOCTYPE html>
<html lang="en">

	<head>

		<meta charset="utf-8">
		<title>קופונופש</title>
		<base href='<?php echo URL::to('/'); ?>/' />
		<?php echo stylesheet_link_tag(); ?>

	</head>

	<body>

	  
	  
	  <?php echo javascript_include_tag(); ?>

	</body>

</html>
<!-- 
	<script type="text/x-handlebars">
	  	{{outlet modal}}
	  	{{#if App.logedin}}
				<div id="content">
					
					<nav class="navbar navbar-inverse">
						<ul class="nav navbar-nav pull-left">
							<li>{{#link-to 'logout'}}יציאה{{/link-to}}</li>
						</ul>
			    </nav>

					{{outlet}}
				</div>
			{{/if}}	
	  </script>
 -->