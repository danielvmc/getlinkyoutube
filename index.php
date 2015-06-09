<?php

require 'GetLinkYoutube.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form action="" method="get">
        <input type="text" name="id">
        <input type="submit" value="Generate Links">
    </form>
</body>
</html>
<?php
if (isset($_GET['id'])) {
    if ($_GET['id'] == '') {
        echo "Insert link to generate";
    } else {
        $linkGenenator = new GetLinkYoutube();
        $linkGenenator->extractPlaylistId($_GET['id']);
        $linkGenenator->output();
    }
}
?>
