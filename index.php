<?php
/**
 * © 2026 WebHubCode
 * https://webhubcode.com
 * License: MIT
 */

$message = '';
$compressedFileUrl = '';
$originalSize = 0;
$compressedSize = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_image'])) {
    $source_file = $_FILES['upload_image']['tmp_name'];
    $file_name = $_FILES['upload_image']['name'];
    $error = $_FILES['upload_image']['error'];

    if ($error === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed_ext)) {
            // Create an uploads directory if it doesn't exist
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $destination = $upload_dir . 'compressed_' . time() . '_' . $file_name;
            $quality = isset($_POST['quality']) ? (int)$_POST['quality'] : 60; // Default 60%

            $originalSize = filesize($source_file);

            // Function to compress image
            function compressImage($source, $destination, $quality) {
                $info = getimagesize($source);
                if (!$info) return false;

                if ($info['mime'] == 'image/jpeg') {
                    $image = imagecreatefromjpeg($source);
                    imagejpeg($image, $destination, $quality);
                } elseif ($info['mime'] == 'image/gif') {
                    $image = imagecreatefromgif($source);
                    imagegif($image, $destination); // GIF doesn't use standard quality scale
                } elseif ($info['mime'] == 'image/png') {
                    $image = imagecreatefrompng($source);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    // Convert 0-100 quality scale to 0-9 PNG compression level
                    $pngQuality = round((100 - $quality) / 100 * 9);
                    imagepng($image, $destination, $pngQuality);
                }
                
                imagedestroy($image);
                return $destination;
            }

            $compressed = compressImage($source_file, $destination, $quality);

            if ($compressed) {
                $compressedSize = filesize($destination);
                $compressedFileUrl = $destination;
                $message = "<div class='alert success'>Image compressed successfully!</div>";
            } else {
                $message = "<div class='alert error'>Failed to compress the image.</div>";
            }
        } else {
            $message = "<div class='alert error'>Invalid format. Please upload JPG, PNG, or GIF.</div>";
        }
    } else {
        $message = "<div class='alert error'>Error uploading the file.</div>";
    }
}

// Helper to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image99Tools - Image Compressor</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input[type="file"] {
            padding: 10px;
            border: 2px dashed #ccc;
            border-radius: 5px;
            cursor: pointer;
        }
        .quality-wrapper {
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="range"] {
            width: 100%;
        }
        button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #2ecc71;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .result {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .download-btn {
            display: inline-block;
            margin-top: 10px;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .download-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Image Compressor</h1>
    <?php echo $message; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="upload_image" accept="image/jpeg, image/png, image/gif" required>
        
        <div class="quality-wrapper">
            <label for="quality">Compression Quality: <span id="qualityVal">60</span>%</label>
            <input type="range" id="quality" name="quality" min="10" max="100" value="60" oninput="document.getElementById('qualityVal').innerText = this.value">
        </div>

        <button type="submit">Compress Image</button>
    </form>

    <?php if ($compressedFileUrl): ?>
        <div class="result">
            <h3>Compression Details</h3>
            <p>Original Size: <strong><?php echo formatBytes($originalSize); ?></strong></p>
            <p>Compressed Size: <strong><?php echo formatBytes($compressedSize); ?></strong></p>
            <p>Saved: <strong><?php echo round(100 - ($compressedSize / $originalSize * 100), 1); ?>%</strong></p>
            <a href="<?php echo $compressedFileUrl; ?>" download class="download-btn">Download Compressed Image</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
