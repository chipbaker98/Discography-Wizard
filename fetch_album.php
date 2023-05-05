<?php

require_once 'DbClass.php';

if (isset($_POST['artistID']) && isset($_POST['songName'])) {
    $artistID = $_POST['artistID'];
    $songName = $_POST['songName'];

    $db = new DbClass();
    $db->establishConnection();
    $connectionStatus = $db->checkConnection();

    if ($connectionStatus === "Connected successfully") {
        $albums = $db->GetAlbumsByArtistAndSong($artistID, $songName);
        echo $albums;
    } else {
        echo "No Albums Found";
    }
} else {
    echo "Invalid request";
}
