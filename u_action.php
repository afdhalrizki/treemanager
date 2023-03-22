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
    $id = (int)$_POST['id'];

    $sql = 'UPDATE tm SET name=? WHERE id=?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $conn->close();

    $data = array("id"=>$id, "name"=>$name);
    echo json_encode($data);
?>