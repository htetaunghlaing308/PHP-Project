<?php
require_once "../partials/auth.php";require_once "../config/database.php";$page_title="New Quotation";$customers=$conn->query("SELECT * FROM customers ORDER BY name");$materials=$conn->query("SELECT * FROM materials ORDER BY name");$mat=[];while($m=$materials->fetch_assoc())$mat[]=$m;
if($_SERVER['REQUEST_METHOD']==='POST'){
 $customer=(int)$_POST['customer_id'];$project=trim($_POST['project']);$install=(float)$_POST['installation'];$delivery=(float)$_POST['delivery'];$discount=(float)$_POST['discount'];$taxRate=(float)$_POST['tax_rate'];$names=$_POST['material_name'];$mids=$_POST['material_id'];$widths=$_POST['width'];$heights=$_POST['height'];$qtys=$_POST['quantity'];$prices=$_POST['price'];$subtotal=0;$items=[];
 foreach($names as $i=>$name){$w=(float)$widths[$i];$h=(float)$heights[$i];$q=(float)$qtys[$i];$p=(float)$prices[$i];$area=$w*$h;$amount=$area*$q*$p;$subtotal+=$amount;$items[]=[$mids[$i]?intval($mids[$i]):null,$name,$w,$h,$q,$p,$area,$amount];}
 $base=max(0,$subtotal+$install+$delivery-$discount);$tax=$base*$taxRate/100;$total=$base+$tax;$no='SG-'.date('Y').'-'.strtoupper(substr(uniqid(),-6));
 $st=$conn->prepare("INSERT INTO quotations(quotation_no,customer_id,project,subtotal,installation,delivery,discount,tax_rate,tax_amount,total) VALUES(?,?,?,?,?,?,?,?,?,?)");$st->bind_param("sisddddddd",$no,$customer,$project,$subtotal,$install,$delivery,$discount,$taxRate,$tax,$total);$st->execute();$qid=$conn->insert_id;
 $it=$conn->prepare("INSERT INTO quotation_items(quotation_id,material_id,material_name,width,height,quantity,price,area,amount) VALUES(?,?,?,?,?,?,?,?,?)");
 foreach($items as $x){$it->bind_param("iissddddd",$qid,$x[0],$x[1],$x[2],$x[3],$x[4],$x[5],$x[6],$x[7]);$it->execute();}
 header("Location:view.php?id=".$qid);exit;
}
include "../partials/header.php";?>
<div style="display:flex;justify-content:space-between"><div><h1 class="page-title">New Quotation</h1><p class="muted">Calculate and save a signage quotation.</p></div></div>
<form method="post" id="quoteForm"><div class="card"><h2>Customer & Project</h2><div class="grid2"><div class="field"><label>Customer</label><select name="customer_id"><option value="0">Walk-in Customer</option><?php while($c=$customers->fetch_assoc()):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?> — <?=htmlspecialchars($c['phone'])?></option><?php endwhile;?></select></div><div class="field"><label>Project / Address</label><input name="project" placeholder="Project or installation location"></div></div></div>
<div class="card"><h2>Signage Items</h2><div id="items"></div><button type="button" class="btn light" onclick="addQuoteItem()">+ Add Item</button></div>
<div class="card"><h2>Additional Charges</h2><div class="grid2"><div class="field"><label>Installation (MMK)</label><input id="installation" name="installation" type="number" value="0" min="0"></div><div class="field"><label>Delivery (MMK)</label><input id="delivery" name="delivery" type="number" value="0" min="0"></div><div class="field"><label>Discount (MMK)</label><input id="discount" name="discount" type="number" value="0" min="0"></div><div class="field"><label>Tax / Other (%)</label><input id="tax_rate" name="tax_rate" type="number" value="0" min="0" step=".01"></div></div><div class="totals"><div class="totalrow"><span>Subtotal</span><b id="subtotal">0 MMK</b></div><div class="totalrow"><span>Tax</span><b id="tax_amount">0 MMK</b></div><div class="totalrow grand"><span>Total</span><b id="total">0 MMK</b></div></div></div>
<div class="actions"><button class="btn primary">Save Quotation</button><a class="btn light" href="index.php">Cancel</a></div></form>
<script>
const materials=<?=json_encode($mat)?>;
function addQuoteItem(){
 const wrap=document.createElement('div');wrap.className='quote-item item';
 wrap.innerHTML=`<div class="itemgrid"><div class="wide"><label>Material</label><select name="material_name[]" onchange="syncPrice(this)">${materials.map(m=>`<option value="${m.name}" data-id="${m.id}" data-price="${m.price}">${m.name}</option>`).join('')}</select><input type="hidden" name="material_id[]" value="${materials[0]?.id||''}"></div><div><label>Width (ft)</label><input class="width" name="width[]" type="number" step=".01" min="0" value="4"></div><div><label>Height (ft)</label><input class="height" name="height[]" type="number" step=".01" min="0" value="8"></div><div><label>Quantity</label><input class="qty" name="quantity[]" type="number" step=".01" min="1" value="1"></div><div><label>Price / sq.ft</label><input class="price" name="price[]" type="number" min="0" value="${materials[0]?.price||0}"></div><div><label>Amount</label><input class="item-amount" readonly value="0"></div><button type="button" class="remove" onclick="this.closest('.quote-item').remove();recalcAll()">×</button></div>`;
 document.getElementById('items').appendChild(wrap);recalcAll();
}
function syncPrice(sel){const o=sel.options[sel.selectedIndex];const item=sel.closest('.quote-item');item.querySelector('[name="material_id[]"]').value=o.dataset.id;item.querySelector('.price').value=o.dataset.price;recalcAll()}
addQuoteItem();
</script>
<?php include "../partials/footer.php";?>