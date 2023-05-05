<?php

class DbClass {

    public $servername = "127.0.0.1";
    public $username = "root";
    public $password = "";
    public $dbname = "discographywizard";
    public $conn = null;

    //Create connection
    function establishConnection(): void
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname, 3307);
    }

    //Check connection
    function checkConnection() : string
    {
        if ($this->conn->connect_error) {
            return "Connection failed: " . $this->conn->connect_error;
        }
        return "Connected successfully";
    }

    //Populate the dropdown menus from the database
    function GetSongsByArtist($artistID)
{
    $sql = "SELECT so.Song Song
    FROM songs so
    JOIN artists ar
    ON so.artist_ID = ar.ID
    WHERE ar.ID = {$artistID}
    GROUP BY so.ID
    ORDER BY so.song ASC;";
    $result = $this->conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        $song = "<option>Select Song</option>";
        while($row = $result->fetch_assoc()) {
            $song .= "<option>{$row['Song']}</option>";
        }
    }
    return $song;
}

    function GetAlbumsByArtistAndSong($artistID, $songName)
{
    $escapedSongName = addslashes($songName);
    $sql = "SELECT
    al.Album Album,
    CASE
    WHEN al.Album_Type = 1 then 'Album'
    WHEN al.Album_Type = 2 then 'Single'
    WHEN al.Album_Type = 3 then 'EP'
    WHEN al.Album_Type = 4 then 'Compilation'
    ELSE 'N/A'
    END AS 'Type'
FROM albums al
JOIN artists ar
    ON al.Artist_ID = ar.ID
JOIN songs so
    ON ar.ID = so.Artist_ID
WHERE so.Song = '{$escapedSongName}'
AND ar.ID = {$artistID}
AND (
    al.ID = so.Album_ID_1
    OR al.ID = so.Album_ID_2
    OR al.ID = so.Album_ID_3
    OR al.ID = so.Album_ID_4
    OR al.ID = so.Album_ID_5
    OR al.ID = so.Album_ID_6
    OR al.ID = so.Album_ID_7
    OR al.ID = so.Album_ID_8
    OR al.ID = so.Album_ID_9
    OR al.ID = so.Album_ID_10
)
order by al.Album_Type ASC;";
    $result = $this->conn->query($sql);
    $album = ""; // Initialize the variable with an empty string
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $album .= "{$row['Album']} ({$row['Type']})<br>";
        }
    }
    return $album;
}
}
?>
