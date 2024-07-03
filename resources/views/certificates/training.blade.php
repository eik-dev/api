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
            font-size: 60px;
            color: #1083ac;
            position: absolute;
            top: 280px;
            font-family: DancingScript;
        }
        .number{
            font-size: 20px;
            line-height: 28px;
            position: absolute;
            bottom: 30px;
            left: 150px;
        }
        .info{
            width: 75%;
            text-align: center;
            position: absolute;
            top: 420px;
            left: 120px;
        }
        .date{
            width: 100%;
            text-align: center;
            position: absolute;
            bottom: 105px;
            font-size: 18px;
            line-height: 28px;
        }
        .QRcode{
            position: absolute;
            bottom: 80px;
            right: 96px;
        }
    </style>
</head>
<body>
    <div class="content">
        <p class="name">{{$name}}</p>
        <p class="number">{{$number}}</p>
        <p class="info">
        {{'For attending the EIK Webinar on Developing Quality Environment Audit Report On 31st January 2024. Your Continuous Professional Developments Points are Two (2) Units.'}}
        </p>
    </div>
    <img class="QRcode" src="data:image/png;base64,<?php echo base64_encode(QrCode::format('png')->size(100)->generate($qrData)); ?>" alt="QRcode">
    <p class="date">Date {{'31st January 2024'}}</p>
</body>
</html>
 