


<?php
include_once "../konfiguracija.php";
include 'includes/head.php';
include 'includes/izbornik.php';

if(isset($_GET['add'])){
$brandQuery= $veza->prepare("SELECT * FROM brand ORDER BY brand;");
$brandQuery->execute();
$parentQuery =$veza->prepare("SELECT* FROM categories WHERE parent = 0 ORDER BY category;");
$parentQuery->execute();

?>

<h2 class ="text-center">Add A New Product</h2><hr>

<form action ="products.php?add=1" method="POST" enctype="multipart/form-data" >
  <div class="row">
   <div class="large-6 columns ">
     <label for="title">Title*:
        <input type="text" name="title" class="former" id="title" placeholder="large-12.columns" value="<?=((isset($_POST['title']))?sanitize($_POST['title']):'');?>"/>
      </label>
    </div>
    <div class="large-6 columns">
       <label for="brand">Brand*: </label>
       <select class="form-group" id="brand" name="brand">
            <option value=""<?=((isset($_POST['brand'])&& $_POST['brand']== '') ?' selected': '');?></option>
            <?php  while ($brand = $brandQuery->fetch(PDO::FETCH_ASSOC)): ?>
          <option value="<?=$brand['id']; ?>"<?= ((isset($_POST['brand'])&& $_POST['brand']==$brand['id'])?' selected':''); ?>><?php echo $brand['brand']; ?></option>
          <?php endwhile; ?>
       </select>
</div>

    <div class="large-6 columns ">
      <label for="parent">Parent Category*:
         <select class="form-group" id="parent" name="parent">
         <option value =""<?=((isset($_POST['parent'])&& $_POST['parent']== '')?' selected':'');?>></option>

         <?php while($parent = $parentQuery->fetch(PDO::FETCH_ASSOC)): ?>
           <option value="<?=$parent['id'];?>"<?=((isset($_POST['parent']) && $_POST['parent'] == $parent['id'])?' select':'');?>><?=$parent['category'];?></option>

         <?php endwhile; ?>

       </label>
       </select>
     </div>
     <div class="large-6 columns ">
       <label for="child">Child Category:*</label>
   			<select id="child" name="child" class="form-control">
   </select>
     </div>
     </div>

</form>

<?php }else{




$format = new NumberFormatter("en_US",NumberFormatter::CURRENCY);
$izraz = $veza->prepare("SELECT * FROM products WHERE deleted = 0;");
$presults= $izraz->execute();
if(isset($_GET['featured'])){
  $id = (int)$_GET['id'];
  $featured=(int)$_GET[featured];
  $izdvojeni = $veza->prepare("UPDATE products SET featured = '$featured' WHERE id='$id';");
  $izdvojeni->execute();
   header('Location: products.php');
}



?>


<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="button pull-right" id="">Add Product</a><div class="clearfix"></div>
<hr>





<table class="responsive-card-table unstriped">
  <thead>
    <tr>
      <th></th>
      <th>Product</th>
      <th>Price</th>
      <th>Category</th>
      <th>Featured</th>
      <th>Sold</th>
    </tr>
  </thead>
  <tbody>
     <?php   while ($product = $izraz->fetch(PDO::FETCH_ASSOC)):
             $childId = $product['categories'];
              $catSql = $veza->prepare("SELECT * FROM categories WHERE id='$childId';");
              $result= $catSql->execute();
              $child = $catSql->fetch(PDO::FETCH_ASSOC);
              $parentId = $child['parent'];
                $parentSql = $veza->prepare("SELECT * FROM categories WHERE id='$parentId';");
               $parentSql->execute();
                $parent =$parentSql->fetch(PDO::FETCH_ASSOC);
                $category = $parent['category'].'~'.$child['category'];
       ?>
           <tr>
              <td>
                   <a href="products.php?edit=<?=$product['id'];?>" class ="button tiny"><i class="fas fa-pen-square fa-2x"></i></a>
                   <a href="products.php?delete=<?=$product['id'];?>" class ="button tiny"><i class="fas fa-trash-alt fa-2x"></i></a>
              </td>
              <td><?=$product['title'];?></td>
              <td><?php echo $format->format ($product['price']); ?></td>
              <td><?php echo $category;?></td>
              <td><a href="products.php?featured=<?=(($product['featured']== 0)?'1':'0');?>&id=<?=$product['id'];?>"
                class ="button tiny"><i class="fas fa-<?=(($product['featured']==1)?'minus':'plus');?> fa-2x " ></i>
              </a>&nbsp <?=(($product['featured']==1)?'Featured Product': '');?></td>
              <td>0</td>

           </tr>
     <?php endwhile; ?>
  </tbody>
</table>


<?php  } include_once 'includes/scripts.php';?>
<?php include_once 'includes/podnozje.php'; ?>