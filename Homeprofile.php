<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Profiles</title>
    <link rel="stylesheet" type="text/css" href="stylesheet.css"> 
</head>
<body>
    <header>
        <div class="container">
            <h1>Meet Our Team</h1>
        </div>
    </header>

    <div class="container">
        <?php
        $profiles = [
            ["name" => "Rennalien Oliva", "position" => "Team Leader", "email" => "olivarennalien_bsit@plmun.edu.ph", "image" => "rennalien.jfif"],
            ["name" => "Ellyza Galindo", "position" => "Developer", "email" => "galindoellyza_bsit@plmun.edu.ph", "image" => "Ellyza.jfif"],
            ["name" => "Jeric Mendoza", "position" => "Developer", "email" => "mendozajeric_bsit@plmun.edu.ph", "image" => "Jeric.jfif"],
            ["name" => "Sarahmel Ocado", "position" => "Designer", "email" => "ocadosarahmel_bsit@plmun.edu.ph", "image" => "sarahmel.jpg"],
            ["name" => "McEphraem San Jose", "position" => "Designer", "email" => "sanjosemcephraem_bsit@plmun.edu.ph", "image" => "McEphraem.jfif"]
        ];

        foreach ($profiles as $profile) {
            echo '<div class="profile">';
            echo '<img src="image/' . $profile["image"] . '" alt="' . $profile["name"] . '">';
            echo '<h2>' . $profile["name"] . '</h2>';
            echo '<p>Position: ' . $profile["position"] . '</p>';
            echo '<p><i>IE account: ' . $profile["email"] . '</i></p>';
            echo '</div>';
        }
        ?>
    </div>
    
    <button onclick="goBack()" class="back-button">Go Back</button>
  
     <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <script src="scripts.js" defer></script>
</body>
</html>
