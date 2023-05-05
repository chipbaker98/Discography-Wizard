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

$myClass = new DbClass();
$myClass->establishConnection();
$connectionStatus = $myClass->checkConnection();

// Function to get the selected option from a dropdown menu
function getSelectedOption($name) {
    if(isset($_POST[$name])) {
        return $_POST[$name];
    }
    return null;
}

// Store the selected option from the Kanye West and Drake dropdown menus in variables
$selectedKanyeOption = getSelectedOption('KanyeWest');
$selectedDrakeOption = getSelectedOption('Drake');
$selectedIceSpiceOption = getSelectedOption('IceSpice');



if($connectionStatus === "Connected successfully")
{
    $KanyeWestSongs = $myClass->GetSongsByArtist(1);
    $DrakeSongs = $myClass->GetSongsByArtist(2);
    $IceSpiceSongs = $myClass->GetSongsByArtist(3);


    if ($selectedKanyeOption) {
        $KanyeWestAlbums = $myClass->GetAlbumsByArtistAndSong(1, $selectedKanyeOption);
    }

    if ($selectedDrakeOption) {
        $DrakeAlbums = $myClass->GetAlbumsByArtistAndSong(2, $selectedDrakeOption);
    }

    if ($selectedIceSpiceOption) {
        $IceSpiceAlbums = $myClass->GetAlbumsByArtistAndSong(3, $selectedIceSpiceOption);
    }
}

else
{
    $KanyeWestSongs = "No Songs Found";
    $DrakeSongs = "No Songs Found";
    $IceSpiceSongs = "No Songs Found";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discography Wizard</title>

    <style>
        .row {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .column {
            flex: 1;
            max-width: 30%;
            box-sizing: border-box;
            padding: 0 10px;
        }
        .h1 {
            text-align: center;
            width: 100%;
        }
        .Artists {
            width: 100px;
            border-radius: 50px;
        }
        .img-container {
            text-align: center;
        }

        .center-text {
            text-align: center;
            width: 100%;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        // Get the dropdown elements
        var kanyeWestDropdown = $('select[name=KanyeWest]');
        var drakeDropdown = $('select[name=Drake]');
        var icespiceDropdown = $('select[name=IceSpice]');

        // Handle dropdown change events
        kanyeWestDropdown.on('change', function () {
            var selectedOption = kanyeWestDropdown.val();
            fetchAlbum(1, selectedOption, 'kanye-albums');
            $('#drake-albums').html(''); // Clear Drake's albums when selecting another artist's song
            $('#icespice-albums').html(''); // Clear Ice Spice's albums when selecting another artist's song
        });

        drakeDropdown.on('change', function () {
            var selectedOption = drakeDropdown.val();
            fetchAlbum(2, selectedOption, 'drake-albums');
            $('#kanye-albums').html(''); // Clear Kanye West's albums when selecting another artist's song
            $('#icespice-albums').html(''); // Clear Ice Spice's albums when selecting another artist's song
        });

        icespiceDropdown.on('change', function () {
            var selectedOption = icespiceDropdown.val();
            fetchAlbum(3, selectedOption, 'icespice-albums');
            $('#kanye-albums').html(''); // Clear Kanye West's albums when selecting another artist's song
            $('#drake-albums').html(''); // Clear Drake's albums when selecting another artist's song
        });

        function fetchAlbum(artistID, songName, targetElementID) {
            $.ajax({
                url: 'fetch_album.php',
                type: 'POST',
                data: {
                    artistID: artistID,
                    songName: songName
                },
                success: function (response) {
                    $('#' + targetElementID).html(response);
                }
            });
        }
    });
</script>
</head>
<body style="background-color:aquamarine;">

    <div class="row">
        <h1 class="h1">Discography Wizard</h1>
    </div>

    <div class="center-text">
        <p>What is Discography Wizard?<p>
        <p>Discography Wizard is a tool that shows you the albums, singles, EP's, or compilations a song is on.<p>
    </div>

    <div class="row">

        <div class="column">
            <div class="img-container">
            <img class="Artists" src="Images/Artists/KanyeWest.jpg">
            <div class="img-container">
                <p>Kanye West</p>
                <select name="KanyeWest">
                    <?php
                        echo $KanyeWestSongs;
                    ?>
                </select>
                <div id="kanye-albums"></div>
            </div>
        </div>
    </div>

        <div class="column">
            <div class="img-container">
                <img class="Artists" src="Images/Artists/Drake.jpg">
                <div class="img-container">
                    <p>Drake</p>
                    <select name="Drake">
                        <?php
                            echo $DrakeSongs;
                        ?>
                    </select>
                    <div id="drake-albums"></div>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="img-container">
                <img class="Artists" src="Images/Artists/IceSpice.jpg">
                <div class="img-container">
                    <p>Ice Spice<p>
                    <select name="IceSpice">
                        <?php
                            echo $IceSpiceSongs;
                        ?>
                    </select>
                    <div id="icespice-albums"></div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
