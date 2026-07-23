<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | Indian Panorama CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        /*======================
            404 page
        =======================*/

        body {
            margin: 0;
        }

        .page_404 {
            padding: 40px 0;
            font-family: "Arvo", serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .page_404 img {
            width: 100%;
        }

        .four_zero_four_bg {
            background-image: url(https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif);
            height: 400px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            animation: rise .6s ease-out;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .four_zero_four_bg h1 {
            font-size: 90px;
            font-weight: 700;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #2563eb, #8b5cf6);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .contant_box_404 {
            margin-top: -50px;
            animation: rise .7s ease-out;
        }

        .contant_box_404 h3 {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: .3px;
            margin-bottom: 12px;
        }

        .contant_box_404 p {
            font-size: 16px;
            color: #64748b;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-10 offset-sm-1 text-center">
                        <div class="four_zero_four_bg">
                            <h1 class="text-center">404</h1>
                        </div>

                        <div class="contant_box_404">
                            <h3 class="h2">
                                Look like you're lost
                            </h3>

                            <p>the page you are looking for not avaible!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
