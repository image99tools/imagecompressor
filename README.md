# Image Compressor

A lightweight, single-file PHP image compression tool created by [WebHubCode](https://webhubcode.com). This tool allows users to easily upload JPEG, PNG, or GIF images, adjust the compression quality via a simple visual slider, and instantly download the optimized file.

## Features

* **Single-File Solution:** All logic, HTML, and CSS are cleanly contained within `index.php`.
* **Multi-Format Support:** Compresses `.jpg`, `.jpeg`, `.png`, and `.gif` files natively.
* **Interactive UI:** Includes a quality slider (10% to 100%) to help balance file size and image fidelity.
* **Detailed Statistics:** Displays the original file size, the newly compressed file size, and the exact percentage of space saved.
* **Auto-Directory Management:** Automatically creates an `uploads/` folder on your server to securely store processed images.

## Requirements

To run this script on your web server, ensure you have the following:
* **PHP** (Version 7.0 or higher recommended)
* **GD Library** enabled (the `php-gd` extension is required for PHP to process and compress the images)
* **Write Permissions:** Ensure the directory hosting `index.php` has the correct write permissions so the script can create the `uploads/` folder and save the processed files.

## Installation & Usage

1. Clone this repository or simply download the `index.php` file.
2. Place the `index.php` file into your web server's document root (e.g., `htdocs`, `www`, or `/var/www/html`).
3. Access the script via your web browser (e.g., `http://localhost/imagecompressor/`).
4. Upload an image, select your desired compression quality, and click **Compress Image**.
5. Download your newly optimized image directly from the success page.

## License

This project is licensed under the MIT License - see the code for details.

---
© 2026 [WebHubCode](https://webhubcode.com). All rights reserved.
