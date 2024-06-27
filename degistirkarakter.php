<?php
$servername = "localhost";
$username = "eko_tablo";
$password = "**";
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

while ($table_row = $tables_result->fetch_row()) {
    $table = $table_row[0];
    echo "Tablo: $table<br>"; // Hata ayıklama için tablo adını ekrana yazdır

    // Tablo sütunlarını al
    $columns_result = $conn->query("SHOW COLUMNS FROM `$table`");
    if (!$columns_result) {
        echo "Sütunlar alınamadı: " . $conn->error . "<br>";
        continue;
    }

    while ($column_row = $columns_result->fetch_assoc()) {
        $column = $column_row['Field'];
        $type = $column_row['Type'];
        echo "Sütun: $column, Tip: $type<br>"; // Hata ayıklama için sütun adını ve tipini ekrana yazdır

        // Sadece metin sütunlarını kontrol et (char, varchar, text, vb.)
        if (strpos($type, 'char') !== false || strpos($type, 'text') !== false) {
            $update_sql = "UPDATE `$table` SET `$column` = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(CONVERT(`$column` USING utf8mb4), 'Ã‡', 'Ç'), 'Ã§', 'ç'), 'Ä°', 'İ'), 'Ä±', 'ı'), 'Ã–', 'Ö'), 'Ã¶', 'ö'), 'Ãœ', 'Ü'), 'Ã¼', 'ü'), 'ÅŸ', 'ş'), 'Åž', 'Ş'), 'ÄŸ', 'ğ'), 'Äž', 'Ğ')";
            echo "Çalıştırılan sorgu: $update_sql<br>"; // Hata ayıklama için sorguyu ekrana yazdır
            if ($conn->query($update_sql) === TRUE) {
                echo "Tablo: $table, Sütun: $column - Güncelleme başarılı.<br>";
            } else {
                echo "Hata: $table, Sütun: $column - " . $conn->error . "<br>";
            }
        }
    }
}

// Bağlantıyı kapat
$conn->close();
?>
