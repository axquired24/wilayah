<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <title>Daftar Map yang tersedia</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
        }
        ul li a:link, ul li a:visited  {
            display: inline-block;
            padding: 4px;
            text-transform: uppercase;            
            text-decoration: none;
            color: blue;
        }

        ul li a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h3>Daftar Map yang tersedia</h3>
    <small>- sementara data tematik hanya di file jabar_* 
    <br>- harap tunggu map dan file tematik di load sampai selesai, kecepatan load tergantung koneksi</small>
    <ul>
        @foreach($files as $f)
        <li><a href="{{ $f->url }}">{{ $f->name }}</a></li>
        @endforeach
    </ul>
</body>
</html>