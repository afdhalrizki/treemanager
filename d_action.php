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

    $id = (int)$_POST['id'];

    $sql = 'DELETE FROM tm WHERE id=?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $conn->close();

    $data = array("id"=>$id);
    echo json_encode($data);
?>