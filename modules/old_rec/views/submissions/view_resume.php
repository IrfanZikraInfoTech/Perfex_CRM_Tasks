<?php $baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer</title>
    <style>
    *{margin:0px; padding: 0px;}
    body{overflow:hidden;}    
    </style>
</head>
<body>
    <object data="<?php echo $baseUrl . 'crm-2.0/uploads/resumes/'.$file_name; ?>" type="application/pdf" style="width:100%; height:100vh;">
    <p>It appears your browser doesn't support PDFs. <a href="<?php echo $baseUrl . 'crm-2.0/uploads/resumes/'.$file_name; ?>">Download the resume</a>.</p>
</object>
</body>
</html>