<?php
require_once "auth.php";require_once "database.php";
$company=['name'=>'SIGNAGEPRO','phone'=>'','email'=>'','address'=>''];
$company_file=__DIR__."/company.php";
if(file_exists($company_file)){ $x=require $company_file; if(is_array($x)) $company=array_merge($company,$x); }$id=(int)($_GET['id']??0);$q=$conn->query("SELECT q.*,c.name customer,c.phone,c.email,c.address FROM quotations q LEFT JOIN customers c ON q.customer_id=c.id WHERE q.id=$id")->fetch_assoc();if(!$q)die("Quotation not found");$items=$conn->query("SELECT * FROM quotation_items WHERE quotation_id=$id");$page_title="Quotation ".$q['quotation_no'];include "header.php";?>
<div class="actions print-hide" style="margin-bottom:16px"><a class="btn light" href="index.php">← Back</a><button class="btn primary" onclick="window.print()">Print / Save PDF</button></div>
<div class="voucher"><div class="voucher-head">
<div class="company-brand">
  <img class="quotation-logo" src="bridge-logo.png" alt="<?=htmlspecialchars($company['name'])?>">
  <div class="company-copy">
    <h1><?=htmlspecialchars($company['name'])?></h1>
    <div class="muted">SIGNAGE QUOTATION</div>
    <?php if(trim($company['phone'])!==''): ?><div class="company-contact">Phone: <?=htmlspecialchars($company['phone'])?></div><?php endif; ?>
    <?php if(trim($company['address'])!==''): ?><div class="company-contact">Address: <?=htmlspecialchars($company['address'])?></div><?php endif; ?>
  </div>
</div>
<div style="text-align:right"><b><?=$q['quotation_no']?></b><br><small><?=date('d M Y',strtotime($q['created_at']))?></small></div>
</div>
<div class="voucher-info"><div class="info-box"><b>Customer</b><br><?=htmlspecialchars($q['customer']??'Walk-in Customer')?><br><?=htmlspecialchars($q['phone']??'')?></div><div class="info-box"><b>Project / Address</b><br><?=htmlspecialchars($q['project'])?><br><?=htmlspecialchars($q['address']??'')?></div></div>
<table class="table"><tr><th>Material</th><th>Size</th><th>Qty</th><th>Price/sq.ft</th><th class="right">Amount</th></tr><?php while($i=$items->fetch_assoc()):?><tr><td><?=htmlspecialchars($i['material_name'])?></td><td><?=$i['width']?> × <?=$i['height']?> ft</td><td><?=$i['quantity']?></td><td><?=number_format($i['price'])?></td><td class="right"><?=number_format($i['amount'])?> MMK</td></tr><?php endwhile;?></table>
<div class="totals"><div class="totalrow"><span>Subtotal</span><b><?=number_format($q['subtotal'])?> MMK</b></div><div class="totalrow"><span>Installation</span><b><?=number_format($q['installation'])?> MMK</b></div><div class="totalrow"><span>Delivery</span><b><?=number_format($q['delivery'])?> MMK</b></div><div class="totalrow"><span>Discount</span><b>- <?=number_format($q['discount'])?> MMK</b></div><div class="totalrow"><span>Tax / Other (<?=$q['tax_rate']?>%)</span><b><?=number_format($q['tax_amount'])?> MMK</b></div><div class="totalrow grand"><span>TOTAL</span><b><?=number_format($q['total'])?> MMK</b></div></div>
<p class="muted" style="font-size:11px;margin-top:30px">This quotation is an estimate. Final price may vary after site measurement, artwork approval, material confirmation and installation requirements.</p></div>
<?php include "footer.php";?>