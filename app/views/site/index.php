<!DOCTYPE html>

<html lang="he">

	<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $club->title; ?></title>
    
    <meta name="description" content="<?php echo $club->description; ?>">

    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->

		<base href='<?php echo URL::to('/'); ?>/' />
		
		<?php echo stylesheet_link_tag("site/application.css"); ?>

	</head>

	<body>
	  
	  <?php echo javascript_include_tag("site/manifest.js"); ?>

	</body>

</html>