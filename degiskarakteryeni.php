<?php
$servername = "localhost";
$username = "eko_tablo";
$password = "0***";
$dbname = "eko_tablo";

// Veritabanına bağlan
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Bağlantı karakter setini UTF-8 olarak ayarla
if (!$conn->set_charset("utf8mb4")) {
    die("UTF-8 karakter seti ayarlanamadı: " . $conn->error);
}

// Tüm tabloları al
$tables_result = $conn->query("SHOW TABLES");
if (!$tables_result) {
    die("Tablolar alınamadı: " . $conn->error);
}

// Geçici tablo adı oluştur
$temp_table_name = "temp_" . uniqid();

while ($table_row = $tables_result->fetch_row()) {
    $table = $table_row[0];
    echo "Tablo: $table<br>"; // Hata ayıklama için tablo adını ekrana yazdır

    // Tablodaki metin türündeki sütunları geçici tabloya aktar
    $create_temp_table_sql = "CREATE TABLE $temp_table_name LIKE $table";
    if ($conn->query($create_temp_table_sql) === TRUE) {
        $copy_data_sql = "INSERT INTO $temp_table_name SELECT * FROM $table";
        if ($conn->query($copy_data_sql) === TRUE) {
            // Orjinal tabloyu sil
            $drop_table_sql = "DROP TABLE $table";
            if ($conn->query($drop_table_sql) === TRUE) {
                // Geçici tabloyu orijinal tablo adına yeniden adlandır
                $rename_table_sql = "RENAME TABLE $temp_table_name TO $table";
                if ($conn->query($rename_table_sql) === TRUE) {
                    echo "Tablo: $table - Güncelleme başarılı.<br>";
                } else {
                    echo "Hata: $table - Tablo yeniden adlandırma hatası: " . $conn->error . "<br>";
                }
            } else {
                echo "Hata: $table - Tablo silme hatası: " . $conn->error . "<br>";
            }
        } else {
            echo "Hata: $table - Veri kopyalama hatası: " . $conn->error . "<br>";
        }
    } else {
        echo "Hata: $table - Geçici tablo oluşturma hatası: " . $conn->error . "<br>";
    }
}

// Bağlantıyı kapat
$conn->close();
?>
