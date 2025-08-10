<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .image-item {
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .image-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome to the Top Page</h1>
        <p>This is a placeholder for your top page content.</p>
        
        <p> view dmm websites description </p>
        <a href="gallery/dmm-websites-description.php">DMM Websites Description</a>

        <p>PNG Images from the images folder:</p>
        <a href="gallery/image/image-fold-choose.php">View Images</a>

        <p>Videos and audios from the folder:</p>
        <a href="gallery/video-audio/video-audio-fold-choose.php">View Videos and Audios</a>    


        <br>
            <p> Sending Questionnaire</p>
            <form action="send_questionaire.php" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone"><br><br>
                <label for="question">Question:</label><br>
                <textarea id="question" name="question" rows="4" cols="50"></textarea><br><br>
                <input type="submit" value="Submit">
            </form>
        </br>
    </div>
</body>
</html>