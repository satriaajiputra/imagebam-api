<?php

use ImageBamAPI\ImageBam;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/imagebam.php';

// check if cached class is exists
if(file_exists(IMAGEBAM_CACHE_PATH)) {
    $fl = fopen(IMAGEBAM_CACHE_PATH, 'r');
    $content = null;
    if($fl) {
        while (!feof($fl)) {
            $content .= fread($fl, 9999);
        }
        fclose(($fl));
    }

    // unserialize ImageBam object from cache
    $image = unserialize($content);
} else {

    // make instance
    $image = new ImageBam(API_KEY, API_SECRET);

    // cache the object
    $fl = fopen(IMAGEBAM_CACHE_PATH, 'w');
    fwrite($fl, serialize($image));
    fclose($fl);
}

// if token does not authorized
if(!$image->getAccessToken()) {

    if(isset($_POST['verifier'])) {
        $image->requestAccessToken($_POST['verifier']);
        $fl = fopen(IMAGEBAM_CACHE_PATH, 'w');
        fwrite($fl, serialize($image));
        fclose($fl);

        header("Refresh:0");
    }

?>
<a href="http://www.imagebam.com/sys/oauth/authorize_token?oauth_token=<?= $image->getRequestToken('oauth_token') ?>" target="_blank">Authorize Token</a>

<form action="" method="POST">
    <input type="text" placeholder="Verifier Code" name="verifier"/>
    <button>Submit</button>
</form>

<?php

// if authorized
} else {
    if(isset($_POST['submit'])) {

        $thumb = $_FILES['image'];

        // curlfile instance for secure while uploading image through curl
        $thumb = new CURLFile($thumb['tmp_name'], $thumb['type'], $thumb['name']);

        $data = [
            'content_type' => $_POST['content_type'],

            'thumb_format' => $_POST['thumb_format'],

            'thumb_size' => $_POST['thumb_size'],

            'thumb_cropping' => $_POST['thumb_cropping'],

            'thumb_info' => $_POST['thumb_info'],

            'gallery_id' => $_POST['gallery_id'],

            'response_format' => 'JSON',

            'image' => $thumb,
        ];
        
        // unlink($thumb);

        echo '<pre>';
        print_r($image->uploadImage($data));
        echo '</pre>';

        exit;
    }
?>
<form action="" method="POST" enctype="multipart/form-data">
<fieldset>
    <legend>Upload Image</legend>
    <label>Image</label>
    <input type="file" name="image">
    <br><br>
    <label>Content Type</label>
    <select name="content_type">
        <option value="family">Family Safe Content</option>
        <option value="adult">Adult Content</option>
    </select>
    <br><br>
    <label>Thumb Format</label>
    <select name="thumb_format">
        <option value="JPG">JPG</option>
        <option value="GIF">GIF</option>
    </select>
    <br><br>
    <label>Content Type</label>
    <select name="thumb_size">
        <option value="100x100">100x100</option>
        <option value="150x150">150x150</option>
        <option value="180x180">180x180</option>
        <option value="350x350">350x350</option>
    </select>
    <br><br>
    <label>Thumb Cropping</label>
    <select name="thumb_cropping">
        <option value="0">Keep Aspect Ratio</option>
        <option value="1">Square</option>
    </select>
    <br><br>
    <label>Thumb Info</label>
    <select name="thumb_info">
        <option value="0">No Info</option>
        <option value="1">Place Thumb Info Watermark</option>
    </select>
    <br><br>
    <label>Gallery ID (optional)</label>
    <input type="text" name="gallery_id" placeholder="32 Char">
    <br><br>
    <input type="submit" value="Upload" name="submit"/>
</fieldset>
</form>
<?php
}