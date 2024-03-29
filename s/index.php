<?php 

session_start();
$connect = mysqli_connect("localhost:3307", "root", "", "adstest");

if(isset($_POST["add_to_cart"]))
{
	if(isset($_SESSION["shopping_cart"]))
	{
		$item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
		if(!in_array($_GET["id"], $item_array_id))
		{
            if($_POST['hidden_quantity']>=$_POST['quantity']){
			echo '<script>alert("hidden: '.$_POST["hidden_quantity"].' quantity"'.$_POST['quantity'].')</script>';
			$count = count($_SESSION["shopping_cart"]);
			$item_array = array(
				'item_id'			=>	$_GET["id"],
				'item_name'			=>	$_POST["hidden_name"],
				'item_price'		=>	$_POST["hidden_price"],
				'item_cost_price'		=>	$_POST["hidden_buyprice"],
				'stock_quantity'		=>	$_POST["hidden_quantity"],
				'item_quantity'		=>	$_POST["quantity"]
			);
			$_SESSION["shopping_cart"][$count] = $item_array;
        }
        else{
            echo '<script>alert("You are Adding more items than existing stock. We only have '.$_POST["hidden_quantity"].' in stock. More stock will be available on '.$_POST["hidden_date"].'")</script>';
        }
		}
		else
		{
			echo '<script>alert("Item Already Added to update Quantity remove the item and add again with the new quantity")</script>';
		}
	}
	else
	{
		$item_array = array(
			'item_id'			=>	$_GET["id"],
			'item_name'			=>	$_POST["hidden_name"],
			'item_price'		=>	$_POST["hidden_price"],
			'item_cost_price'		=>	$_POST["hidden_buyprice"],
			'stock_quantity'		=>	$_POST["hidden_quantity"],
			'item_quantity'		=>	$_POST["quantity"]
		);
		$_SESSION["shopping_cart"][0] = $item_array;
	}
}

if(isset($_GET["action"]))
{
	if($_GET["action"] == "delete")
	{
		foreach($_SESSION["shopping_cart"] as $keys => $values)
		{
			if($values["item_id"] == $_GET["id"])
			{
				unset($_SESSION["shopping_cart"][$keys]);
				echo '<script>alert("Item Removed")</script>';
				echo '<script>window.location="index.php"</script>';
			}
		}
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>cart</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	</head>
	<body>
		<br />
		<div class="container">
			<?php
				$query = "SELECT * FROM stock_information";
				$result = mysqli_query($connect, $query);
				if(mysqli_num_rows($result) > 0)
				{
					while($row = mysqli_fetch_array($result))
					{
				?>
			<div class="col-md-4">
				<form method="post" action="index.php?action=add&id=<?php echo $row["StockID"]; ?>">
					<div style="background-color:whitesmoke;">

						<h4 class="text-info"><?php echo $row["StockName"]; ?></h4>

						<h4 class="text-danger">$ <?php echo $row["sellPrice"]; ?></h4>

						<input type="text" name="quantity" value="1" class="form-control" />

						
                        <input type="hidden" name="hidden_quantity" value="<?php echo $row['QuantityInStock']; ?>" />

						<input type="hidden" name="hidden_name" value="<?php echo $row["StockName"]; ?>" />

						<input type="hidden" name="hidden_price" value="<?php echo $row["sellPrice"]; ?>" />
						<input type="hidden" name="hidden_buyprice" value="<?php echo $row["buyPrice"]; ?>" />
						<input type="hidden" name="hidden_date" value="<?php echo $row["nextDeliveryDate"]; ?>" />

						<input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-primary" value="Add to Cart" />

					</div>
				</form>
			</div>
			<?php
					}
				}
			?>
			<div style="clear:both"></div>
			<br />
			<h3>Order Details</h3>
			<div class="table-responsive">
				<table class="table table-bordered">
					<tr>
						<th width="40%">Item Name</th>
						<th width="10%">Quantity</th>
						<th width="20%">Price</th>
						<th width="15%">Total</th>
						<th width="5%">Action</th>
					</tr>
					<?php
					if(!empty($_SESSION["shopping_cart"]))
					{
						$total = 0;
						foreach($_SESSION["shopping_cart"] as $keys => $values)
						{
					?>
					<tr>
						<td><?php echo $values["item_name"]; ?></td>
						<td><?php echo $values["item_quantity"]; ?></td>
						<td>$ <?php echo $values["item_price"]; ?></td>
						<td>$ <?php echo number_format($values["item_quantity"] * $values["item_price"], 2);?></td>
						<td><a href="index.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span class="text-danger">Remove</span></a></td>
					</tr>
					<?php
							$total = $total + ($values["item_quantity"] * $values["item_price"]);
						}
					?>
                    <tr>
						<td colspan="3" align="right">Total : <?php echo number_format($total, 2); ?> </td>
						<td colspan="2">
                        <form action="checkout.php" method="post">
                        <label for="list">Payment :</label>
                        <select id="list" name="paymentStatus">
                            <option value="success">Success</option>
                            <option value="pending">Pending</option>
                            <option value="default">Default</option>
                        </select>
                        <label for="list">Payment :</label>
                        <input type="text" id="customerID" name="customerID" >
                        <input type="submit" name="action" value="checkout">
                        </form>
                        </td>
					</tr>
					<?php
					}
					?>
						
				</table>
			</div>
		</div>
	</div>
	<br />
	</body>
</html>