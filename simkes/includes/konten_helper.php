<?php

function getTableColumns($conn, $table) {
    $columns = [];
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
        }
    }
    return $columns;
}

function getTableColumnInfo($conn, $table) {
    $columns = [];
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row;
        }
    }
    return $columns;
}

function columnValueForInsert($column, $judul) {
    $name = $column['Field'];
    $type = strtolower($column['Type']);
    if (in_array($name, ['kunci', 'slug'])) {
        return strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($judul)));
    }
    if (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false || strpos($type, 'float') !== false) {
        return 0;
    }
    if (strpos($type, 'date') !== false || strpos($type, 'time') !== false) {
        return '1970-01-01';
    }
    return $judul;
}

function ensureKontenTable($conn) {
    $exists = mysqli_query($conn, "SHOW TABLES LIKE 'konten'");
    if (!$exists) {
        return;
    }

    if (mysqli_num_rows($exists) === 0) {
        mysqli_query($conn, "CREATE TABLE konten (
            id_konten INT AUTO_INCREMENT PRIMARY KEY,
            judul_konten VARCHAR(255) NOT NULL UNIQUE,
            isi_konten TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        return;
    }

    $columns = getTableColumns($conn, 'konten');
    if (!in_array('judul_konten', $columns) && in_array('judul', $columns)) {
        mysqli_query($conn, "ALTER TABLE konten ADD COLUMN judul_konten VARCHAR(255) NOT NULL");
        mysqli_query($conn, "UPDATE konten SET judul_konten = judul");
    }
    if (!in_array('isi_konten', $columns) && in_array('isi', $columns)) {
        mysqli_query($conn, "ALTER TABLE konten ADD COLUMN isi_konten TEXT NOT NULL");
        mysqli_query($conn, "UPDATE konten SET isi_konten = isi");
    }
    if (!in_array('judul_konten', $columns) && !in_array('judul', $columns)) {
        mysqli_query($conn, "ALTER TABLE konten ADD COLUMN judul_konten VARCHAR(255) NOT NULL");
    }
    if (!in_array('isi_konten', $columns) && !in_array('isi', $columns)) {
        mysqli_query($conn, "ALTER TABLE konten ADD COLUMN isi_konten TEXT NOT NULL");
    }
}

function ensureKontenRows($conn) {
    $defaults = [
        'Hero' => 'Melayani kesehatan masyarakat Desa Krapyak dengan sepenuh hati.',
        'Tentang Kami' => 'SIMKES Desa Krapyak hadir sebagai wujud cinta dan kepedulian kami terhadap kesehatan seluruh lapisan masyarakat. Kami percaya bahwa pelayanan kesehatan yang berkualitas harus bisa diakses dengan mudah, nyaman, dan cepat oleh siapa pun.\n\nMelalui sistem ini, kami berupaya menghapus jarak dan waktu antrean yang panjang. Kini, warga Krapyak dapat merencanakan kunjungan kesehatan keluarga hanya dalam beberapa klik dari rumah, sehingga waktu berharga Anda bisa lebih banyak dihabiskan bersama orang tercinta.',
        'Layanan' => 'Pelayanan kesehatan Desa Krapyak tersedia untuk semua layanan utama yang Anda butuhkan.',
        'Booking' => 'Tak perlu antre lama, cukup pesan dari rumah untuk kenyamanan bersama.',
        'Kontak' => 'Kami siap membantu kebutuhan kesehatan Anda.'
    ];

    $columns = getTableColumns($conn, 'konten');
    $columnInfo = getTableColumnInfo($conn, 'konten');
    foreach ($defaults as $judul => $isi) {
        $judulEscaped = mysqli_real_escape_string($conn, $judul);
        if (in_array('judul_konten', $columns)) {
            $exists = mysqli_query($conn, "SELECT 1 FROM konten WHERE judul_konten = '$judulEscaped' LIMIT 1");
        } elseif (in_array('judul', $columns)) {
            $exists = mysqli_query($conn, "SELECT 1 FROM konten WHERE judul = '$judulEscaped' LIMIT 1");
        } else {
            continue;
        }

        if (!$exists || mysqli_num_rows($exists) === 0) {
            $isiEscaped = mysqli_real_escape_string($conn, $isi);
            $insertColumns = [];
            $insertValues = [];

            if (in_array('judul_konten', $columns) && in_array('isi_konten', $columns)) {
                $insertColumns = ['judul_konten', 'isi_konten'];
                $insertValues = [$judulEscaped, $isiEscaped];
            } elseif (in_array('judul', $columns) && in_array('isi', $columns)) {
                $insertColumns = ['judul', 'isi'];
                $insertValues = [$judulEscaped, $isiEscaped];
            } elseif (in_array('judul_konten', $columns)) {
                $insertColumns = ['judul_konten', 'isi_konten'];
                $insertValues = [$judulEscaped, $isiEscaped];
            }

            foreach ($columnInfo as $column) {
                $name = $column['Field'];
                if (in_array($name, $insertColumns)) {
                    continue;
                }
                if ($column['Extra'] === 'auto_increment') {
                    continue;
                }
                if ($column['Null'] === 'NO' && $column['Default'] === null) {
                    $value = mysqli_real_escape_string($conn, columnValueForInsert($column, $judul));
                    $insertColumns[] = $name;
                    $insertValues[] = $value;
                }
            }

            if (!empty($insertColumns)) {
                $columnsList = implode(', ', array_map(function ($c) { return "`$c`"; }, $insertColumns));
                $valuesList = implode(', ', array_map(function ($v) { return "'{$v}'"; }, $insertValues));
                mysqli_query($conn, "INSERT INTO konten ($columnsList) VALUES ($valuesList)");
            }
        }
    }
}

function getKonten($conn, $judul, $default = '') {
    $judulEscaped = mysqli_real_escape_string($conn, $judul);
    $columns = getTableColumns($conn, 'konten');

    if (in_array('isi_konten', $columns) && in_array('judul_konten', $columns)) {
        $result = mysqli_query($conn, "SELECT isi_konten AS isi FROM konten WHERE judul_konten = '$judulEscaped' LIMIT 1");
    } elseif (in_array('isi', $columns) && in_array('judul', $columns)) {
        $result = mysqli_query($conn, "SELECT isi FROM konten WHERE judul = '$judulEscaped' LIMIT 1");
    } else {
        return $default;
    }

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['isi'] ?? $default;
    }
    return $default;
}

function getKontenHtml($conn, $judul, $default = '') {
    return nl2br(htmlspecialchars(getKonten($conn, $judul, $default)));
}
