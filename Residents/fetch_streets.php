<?php
include("db_connect.php");

if(isset($_GET['area_id']) && !empty($_GET['area_id'])) {
    $area_id = intval($_GET['area_id']);
    $stmt = $conn->prepare("SELECT street_id, street_name FROM Streets WHERE area_id = ? ORDER BY sequence_number ASC");
    $stmt->bind_param("i", $area_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $options = '<option value="">-- Select Street --</option>';
    while($row = $result->fetch_assoc()){
        $options .= '<option value="' . $row['street_id'] . '">' . htmlspecialchars($row['street_name']) . '</option>';
    }
    echo $options;
    $stmt->close();
}
?>
