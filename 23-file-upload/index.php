<?php

  $db = new PDO("mysql:host=localhost;dbname=test;charset=utf8mb4", "std", "");

  // read config params from php.ini
  $max_filesize = ini_get("upload_max_filesize") ;
  $max_postsize = ini_get("post_max_size") ;

  echo "<p>Max Upload filesize : $max_filesize</p>" ;
  echo "<p>Max Total postsize : $max_postsize</p>" ;

  //var_dump($_POST) ;
  //var_dump($_FILES) ; 

  if ( $_SERVER["REQUEST_METHOD"] == "POST" && empty($_POST)) {
    $error = "Request exceeds $max_postsize" ; 
  }

  if ( !empty($_POST)) {
    extract($_POST) ;  // $tags
    $photo = upload("photo") ;
    if ( isset($photo["error"])) {
        $error = $photo["error"] ;
    } else {
      $stmt = $db->prepare("insert into album (original, filename, tags) values (?,?,?)");
      $stmt->execute([ $_FILES["photo"]["name"], $photo["filename"], $tags ]) ;
    }
  }

  $photos = $db->query("select * from album order by created_at desc")->fetchAll() ;
  // var_dump($photos) ;

  function upload($filebox) {
    global $max_filesize ;
    if ( isset($_FILES[$filebox])) {
        $file = $_FILES[$filebox] ; 
        // get the extention of uploaded file.
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION)) ;

        // Check Error Cases
        if ( $file["error"] == UPLOAD_ERR_INI_SIZE) {
            $error = "{$file["name"]} : greater than $max_filesize" ; 
        } else if ( $file["error"] == UPLOAD_ERR_NO_FILE) {
            $error = "No file chosen" ;
        } else if ( !in_array($ext, ["jpg", "png", "gif"])) {
            // check the extension
            $error = "{$file["name"]} : Not an image file" ;
        } else {
            // The Upload was completed successfully
            // random filename to prevent filename collision
            $filename = bin2hex(random_bytes(8)) . ".$ext" ; 

            // move uploaded file to application folder ("photos" in this example)
            if (move_uploaded_file($file["tmp_name"], "./photos/" . $filename ) ) {
                return ["filename" => $filename] ;
            } 
            $error = "{$file["tmp_name"]} cannot be moved. (check permissions)" ;
        }
    }
    return ["error" => $error] ;
  }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .error { color: red ; font-style: italic;}
        .container img { width: 300px; height: 200px; object-fit: cover; }
        .container { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-start;}
        .container > div { border:1px solid #CCC; padding: 10px; border-radius: 10px; box-shadow: 0 2px 15px  rgba(0,0,0, 0.2); max-width: 300px;}
        .original { text-align: center; font-weight: bold; margin-bottom: 15px; }
        .tags { display: flex; flex-wrap: wrap; margin-top: 10px;}
        .tags span { padding: 2px 8px; background: #DDD; border-radius: 10px; margin:5px;}
        .date { margin-top: 10px; font-size: 0.9em; font-style: italic;margin-bottom: 10px; text-align: right;}
        
    </style>
</head>
<body>
    <h1>My Photo-Album App</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p>Tags : <input type="text" name="tags" value="<?= !empty($error) ? $tags : '' ?>" autofocus></p>
        <p>Photo : <input type="file" name="photo"></p>
        <p><button>Upload</button></p>
    </form>
    <p class="error">
        <?= $error ?? "" ?>
    </p>

    <div class="container">
        <?php foreach( $photos as $p) : ?>
            <div>
                <div class="original"><?= $p["original"] ?></div> 
                
                
                <div class="date">
                  <?php
                       $date = new DateTime($p["created_at"]) ;
                       echo $date->format("d F Y, H:m") ;
                   ?>
                </div>
                <img src="./photos/<?= $p["filename"] ?>">

                <div class="tags">
                  <?php
                     $tags = explode(" ", $p["tags"]) ;
                     foreach( $tags as $t) {
                        echo "<span>$t</span>" ;
                     }
                  ?>
                </div> 
            </div>
        <?php endforeach ?>
    </div>
</body>
</html>




