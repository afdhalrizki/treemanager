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

    $name = $_POST['name'];
    $parent_id = (int)$_POST['parent_id'];

    if ($parent_id == 0) {
      $sql = "INSERT INTO tm (name, parent_id) VALUES (?, NULL)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $name);
    } else {
      $sql = "INSERT INTO tm (name, parent_id) VALUES (?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("si", $name, $parent_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $last_id = $conn->insert_id;
    $conn->close();
    $data = array("id"=>$last_id, "name"=>$_POST['name'], "parent_id"=>$_POST['parent_id']);
    echo json_encode($data);
?>