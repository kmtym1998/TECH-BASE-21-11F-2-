<?php
    $logos = [
        'Google' => 'https://cdn.vox-cdn.com/thumbor/E9RM8-qg-iyLEAzP4d7tobqI09o=/0x0:2012x1341/1400x933/filters:focal(0x0:2012x1341):no_upscale()/cdn.vox-cdn.com/uploads/chorus_image/image/47070706/google2.0.0.jpg',
        'Facebook' => 'https://1000logos.net/wp-content/uploads/2016/11/New-Facebook-Logo-500x171.jpg',
        'Apple' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/505px-Apple_logo_black.svg.png',
        'Amazon' => 'https://www.marketplace.org/wp-content/uploads/2019/07/ama2.png'
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <head>
        <h1>Title</h1>
    </head>

    <main>
        <?php foreach($logos as $key => $src): ?>
            <div style="display: inline-block; vertical-align: top">
                <div>
                    <h2><?= $key ?></h2>
                    <img src=<?= $src ?> alt="" width="300px" height="auto">
                </div>
            </div>
        <?php endforeach ?>
    </main>


    <footer>
            <hr>
            owari
    </footer>
</body>
</html>