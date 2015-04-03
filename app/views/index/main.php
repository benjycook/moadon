<!doctype html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<base href="<?php echo URL::to('/')."/" ?>">
		<?php echo stylesheet_link_tag("index/application.css"); ?>
		<?php echo javascript_include_tag('index/application.js'); ?>
		<!--[if lte IE 9]>
			<link rel="stylesheet" type="text/css" href="css/ie9.css">
		<![endif]-->
		<?php if(isset($club)) { ?>
			<title><?= $club->name ?></title>
		<?php }
		else { ?>
			<title>קופונופש</title>
		<?php } ?>	
	</head>

	<body style="direction: rtl;">
		<?php include "partials/$template.tpl.php"; ?>
	</body>
</html>