<ul style="list-style:none;padding:50px;direction:rtl;">
	<li style="margin-bottom:10px;">שלום <?= $client['firstName']?> <?=$client['lastName']?>,</li>
	<li style="margin-bottom:10px;">תודה שרכשת במועדונופש, מועדון חברים!</li>
	<li>מספר הזמנה: <?=$orderNum?></li>
	<li>להלן פרטי ההזמנה:</li>
	<?php foreach ($items as $item) { ?>
		<li><?=$item['supplierName']?> - <?= $item['name']?>: <?=$item['qty']?></li>
	<?php } ?>
	<li style="margin-top:10px;"><strong>יש לתאם מראש לפני הגעה.</strong></li>
	<li><strong>יש להציג מספר הזמנה בקופה.</strong></li>
	<li style="margin-top:10px;">מועדונופש, מועדון חברים</li>
</ul>



 