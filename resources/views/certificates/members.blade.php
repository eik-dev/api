<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"> -->
    <style>
        @page {
            size: 1045px 740px;
            margin: 0;
            padding: 0;
        }
        body {
            background-image: url({{ $background }});
            background-repeat: no-repeat;
            width: 1045px;
            height: 740px;
            position: relative;
            font-family: "Montserrat", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
        }
        .content{
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .text-center{
            text-align: center;
        }
        .paragraph{
            line-height: 2.5cap;
        }
        .date{
            position: absolute;
            bottom: 90px;
            right: 240px;
        }
        .QRcode{
            position: absolute;
            width: 48px;
            height: 48px;
            bottom: 112px;
            right: 474px;
        }
    </style>
</head>
<body class="bg-certificate">
    <div class="content">
        <p class="text-center">This is to certify that</p>
        <p class="text-center">{{ $name }}</p>
        <p class="text-center">Member No: {{$number}}</p>
        <p class="paragraph text-center">
            {{$info}}
        </p>
    </div>
    <img class="QRcode" src="data:image/png;base64,<?php echo base64_encode(QrCode::format('png')->size(250)->generate($qrData)); ?>" alt="QRcode">
    <p class="date">{{$date}}</p>
</body>
</html>
 