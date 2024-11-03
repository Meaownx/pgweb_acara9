<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
</head>

<body>
    <?php
    session_start();
    // Create connection
    $conn = new mysqli("localhost", "root", "", "pgweb_acara8");
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if edit action is triggered
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $edit_sql = "SELECT * FROM kecamatan WHERE id = $edit_id";
        $edit_result = $conn->query($edit_sql);
        if ($edit_result->num_rows > 0) {
            $edit_row = $edit_result->fetch_assoc();
        } else {
            echo "No data found.";
            exit();
        }
    } else {
        echo "No edit ID specified.";
        exit();
    }

    // Handle update action
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $kecamatan = $_POST['kecamatan'];
        $longitude = $_POST['longitude'];
        $latitude = $_POST['latitude'];
        $luas = $_POST['luas'];
        $jumlah_penduduk = $_POST['jumlah_penduduk'];

        $update_sql = "UPDATE kecamatan SET kecamatan='$kecamatan', longitude='$longitude', latitude='$latitude', luas='$luas', jumlah_penduduk='$jumlah_penduduk' WHERE id=$id";
        if ($conn->query($update_sql) === TRUE) {
            $_SESSION['message'] = "Record updated successfully."; // Simpan pesan sukses
            header("Location: index.php"); // Redirect back to the main page after updating
            exit();
        } else {
            $_SESSION['error'] = "Error updating record: " . $conn->error; // Simpan pesan error
            header("Location: index.php"); // Redirect back to the main page
            exit();
        }
    }
    ?>

    <h3>Edit Data Kecamatan</h3>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
        <label for="kecamatan">Kecamatan:</label>
        <input type="text" name="kecamatan" value="<?php echo $edit_row['kecamatan']; ?>" required><br>
        <label for="longitude">Longitude:</label>
        <input type="text" name="longitude" value="<?php echo $edit_row['longitude']; ?>" required><br>
        <label for="latitude">Latitude:</label>
        <input type="text" name="latitude" value="<?php echo $edit_row['latitude']; ?>" required><br>
        <label for="luas">Luas:</label>
        <input type="text" name="luas" value="<?php echo $edit_row['luas']; ?>" required><br>
        <label for="jumlah_penduduk">Jumlah Penduduk:</label>
        <input type="text" name="jumlah_penduduk" value="<?php echo $edit_row['jumlah_penduduk']; ?>" required><br>
        <input type="submit" name="update" value="Update">
    </form>
</body>

</html>
