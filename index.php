<?php
// Create connection
$conn = new mysqli("localhost", "root", "", "pgweb_acara8");
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        #map {
            width: 100%;
            height: 600px;
        }

        .container-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .table-container,
        .map-container {
            flex: 1 1 45%;
            min-width: 300px;
        }
    </style>
</head>

<body>
    <div class="container border border-info rounded mt-3 p-3">
        <div class="alert alert-info text-center" role="alert">
            <h2>WEB GIS</h2>
            <h5>KABUPATEN SLEMAN</h5>
        </div>

        <div class="alert alert-info" role="alert">
            <h5>Kecamatan</h5>
        </div>

        <div class="container-content">
            <!-- Kolom Tabel -->
            <div class="table-container">
                <div class="text-end my-3">
                    <a href="input.html" class="btn btn-info">Add Data</a>
                </div>
                <?php
                $sql = "SELECT * FROM kecamatan";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    echo "<table class='table table-striped table-primary table-bordered'><tr>
                        <th>ID</th>
                        <th>Kecamatan</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th>Luas</th>
                        <th>Jumlah Penduduk</th>
                        <th>Aksi</th></tr>";

                    // Output data untuk setiap baris
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["kecamatan"] . "</td>
                            <td>" . $row["longitude"] . "</td>
                            <td>" . $row["latitude"] . "</td>
                            <td>" . $row["luas"] . "</td>
                            <td>" . $row["jumlah_penduduk"] . "</td>
                            <td>
                                <a href='edit.php?edit_id=" . $row["id"] . "' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='?delete_id=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>
                            </td>
                        </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "0 results";
                }
                $conn->close();
                ?>
            </div>

            <!-- Kolom Peta -->
            <div class="map-container">
                <div id="map" class="border border-info rounded"></div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Inisialisasi peta
        var map = L.map("map").setView([-7.7337857, 110.2673721], 12);

        // Tile Layer Base Map
        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        });

        var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        var rupabumiindonesia = L.tileLayer('https://geoservices.big.go.id/rbi/rest/services/BASEMAP/Rupabumi_Indonesia/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Badan Informasi Geospasial'
        });

        // Menambahkan basemap ke dalam peta
        rupabumiindonesia.addTo(map);

        // Layer Group untuk Marker dari Database
        var markerGroup = L.layerGroup();

        <?php
        $conn = new mysqli("localhost", "root", "", "pgweb_acara8");
        $sql = "SELECT * FROM kecamatan";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $longitude = $row['longitude'];
                $latitude = $row['latitude'];
                $info = $row['kecamatan'];
                echo "L.marker([$latitude, $longitude]).addTo(map).bindPopup('$info');";
            }
        }
        $conn->close();
        ?>

        // Tambahkan markerGroup ke dalam peta secara default
        markerGroup.addTo(map);

        // Control Layer
        var baseMaps = {
            "OpenStreetMap": osm,
            "Esri World Imagery": Esri_WorldImagery,
            "Rupa Bumi Indonesia": rupabumiindonesia,
        };

        var overlayMaps = {
            "Marker": markerGroup,
        };

        // Menambahkan Layer Control ke dalam peta
        L.control.layers(baseMaps, overlayMaps, { collapsed: false }).addTo(map);

        // Scale
        var scale = L.control.scale({
            position: "bottomright",
            imperial: false,
        });
        scale.addTo(map);
    </script>
</body>

</html>