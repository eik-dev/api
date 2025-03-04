<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @page {
            size: 1045px 740px;
            margin: 0;
            padding: 0;
        }
        @font-face {
            font-family: DancingScript;
            src: url({{storage_path('fonts/DancingScript-VariableFont_wght.ttf')}});
        }
        body {
            background-image: url({{ $background }});
            background-repeat: no-repeat;
            background-size: 1045px 740px;
            width: 1045px;
            height: 740px;
            position: relative;
            font-family: "Montserrat", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
        }
        .name{
            width: 100%;
            text-align: center;
            font-size: 50px;
            color: #587d33;
            position: absolute;
            top: 260px;
            font-family: DancingScript;
        }
        .number{
            font-size: 11px;
            color: #587d33;
            line-height: 18px;
            position: absolute;
            bottom: 51px;
            left: 525px;
        }
        .info{
            display: none;
            width: 75%;
            text-align: center;
            position: absolute;
            top: 390px;
            left: 220px;
        }
        .date{
            display: none;
            width: 100%;
            text-align: center;
            position: absolute;
            bottom: 105px;
            font-size: 18px;
            line-height: 28px;
        }
        .QRcode{
            position: absolute;
            top: 40%;
            left: 60px;
        }
    </style>
</head>
<body>
    <div class="content">
        <p class="name">{{$name}}</p>
        <p class="number">{{$number}}</p>
        <p class="info">
        {{$info}}
        </p>
    </div>
    <img class="QRcode" src="data:image/png;base64,<?php echo base64_encode(QrCode::format('png')->size(100)->generate($qrData)); ?>" alt="QRcode">
    <p class="date"> {{$date}}</p>
</body>
</html>
 