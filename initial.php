<?php include 'autoload.php' ?>
<?php
$servername = $_ENV['SERVERNAME'];
$username = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];
$dbname = $_ENV['DBNAME'];


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

/*Executing the select query to fetch data from table tab_treeview*/
$sqlqry="SELECT * FROM tm";
$result=mysqli_query($conn, $sqlqry);

/*Defining an array*/
$arrayCountry = array();

while($row = mysqli_fetch_assoc($result)){ 
	$arrayCountry[$row['id']] = array("parent_id" => $row['parent_id'], "name" => $row['name']);
}


?>
	<div class="treeview js-treeview">
			<?php
				$count_data = 0;
				$ul_tag = 1;
				function buildTree($array, $currentParent, $currLevel = 0, $prevLevel = -1) {
					global $count_data;
					global $ul_tag;
					foreach ($array as $categoryId => $category) {
						if ($currentParent == $category['parent_id']) {
							$count_data++;
							if ($currLevel > $prevLevel) {
								echo "<ul>";
								$ul_tag = 1;
							}
							if ($currLevel == $prevLevel) {
								$ul_tag = 1;
								echo "</li>";
							};
							echo '
								<li>
									<div class="treeview__level" data-level="'.$currLevel.'" data-id-abstract="no-'.$count_data.'" id="no-'.$count_data.'">
										<span class="level-title">'.$category['name'].'</span>
										<div class="treeview__level-btns" data-id-real="'.$categoryId.'" data-parent_id="'.$category['parent_id'].'">
											<div class="btn btn-default btn-sm level-add"><span class="fa fa-plus"></span></div>
											<div class="btn btn-default btn-sm level-remove"><span class="fa fa-trash text-danger"></span></div>
											<div class="btn btn-default btn-sm level-same"><span>Add Same Level</span></div>
											<div class="btn btn-default btn-sm level-sub"><span>Add Sub Level</span></div>
											<div class="btn btn-default btn-sm level-rename" data-toggle="modal" data-target="#myModal"><span>Rename Level</span></div>
										</div>
									</div>
							';
							if ($currLevel > $prevLevel) { $prevLevel = $currLevel;}
							$currLevel++; 
							buildTree ($array, $categoryId, $currLevel, $prevLevel);
							$currLevel--;
							if ($ul_tag) { echo '<ul></ul>' ;} 
							
						}
					}

					if ($currLevel == $prevLevel) {echo "</li></ul>"; $ul_tag=0;}
				}

				if (mysqli_num_rows($result)==0) {
					$sql = "INSERT INTO tm (name, parent_id) VALUES ('Level 0', NULL)";

					if ($conn->query($sql) === TRUE) {
						$last_id = $conn->insert_id;
						// echo "New record created successfully. Last inserted ID is: " . $last_id;
					}
					
					/*Executing the select query to fetch data again after inserting first data*/
					$sqlqry="SELECT * FROM tm";
					$result=mysqli_query($conn, $sqlqry);

					/*Defining an array*/
					$arrayCountry = array();

					while($row = mysqli_fetch_assoc($result)){ 
						$arrayCountry[$row['id']] = array("parent_id" => $row['parent_id'], "name" => $row['name']);
					}

				}

				/*Checking is there any records in $result array*/
				if(mysqli_num_rows($result)!=0) {
					/*Calling the recursive function*/
					buildTree($arrayCountry, 0);
				}
			?>
	</div>


<input type="hidden" id="maxLevelId" value="no-<?php echo $count_data ?>">

<?php

$conn->close();

?>

<template id="levelMarkup">
    <li>
        <div class="treeview__level" data-level="A" data-id-abstract="no-xxx" id="no-xxx">
            <span class="level-title">Level 0</span>
            <div class="treeview__level-btns" data-id-real="" data-parent_id="">
                <div class="btn btn-default btn-sm level-add"><span class="fa fa-plus"></span></div>
                <div class="btn btn-default btn-sm level-remove"><span class="fa fa-trash text-danger"></span></div>
                <div class="btn btn-default btn-sm level-same"><span>Add Same Level</span></div>
                <div class="btn btn-default btn-sm level-sub"><span>Add Sub Level</span></div>
                <div class="btn btn-default btn-sm level-rename"  data-toggle="modal" data-target="#myModal"><span>Rename Level</span></div>
            </div>
        </div>
        <ul>
        </ul>
    </li>
</template>	

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalLabel">Rename Level 1 Name</h4>
			</div>
			<div class="modal-body">
				<form action="u_action.php" method="post" id="updateForm">
					<div class="form-group">
						<label for="nameUpdate" class="control-label" id="labelInputModal">New Level Name:</label>
						<input type="text" class="form-control" id="nameUpdate" name="name">
						<input type="hidden" id="idUpdate" name="id">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="renameModalButton" data-id-abstract="">Rename</button>
			</div>
    	</div>
    </div>
</div>

<form action="c_action.php" method="post" id="createForm">
	<input type="hidden" name="abstract_id" id="c_abstract_id">
    <input type="hidden" name="name" id="c_name">
    <input type="hidden" name="parent_id" id="c_parent_id">
</form>

<form action="d_action.php" method="post" id="deleteForm">
    <input type="hidden" name="id" id="d_id">
</form>