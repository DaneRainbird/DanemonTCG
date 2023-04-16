<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Authentication Error</title>

        <style>
            body {
                height: 100%;
                background: #fafafa;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                color: #777;
                font-weight: 300;
            }
            h1 {
                font-weight: lighter;
                letter-spacing: 0.8;
                font-size: 3rem;
                margin-top: 0;
                margin-bottom: 0;
                    color: #222;
                }
            .wrap {
                max-width: 1024px;
                margin: 5rem auto;
                padding: 2rem;
                background: #fff;
                text-align: center;
                border: 1px solid #efefef;
                border-radius: 0.5rem;
                position: relative;
            }
            p {
                margin-top: 1.5rem;
            }
            code {
                background: #fafafa;
                border: 1px solid #efefef;
                padding: 0.5rem 1rem;
                border-radius: 5px;
                display: block;
		    }
            a:active,
            a:link,
            a:visited {
                color: #dd4814;
            }
        </style>
    </head>
    <body>
        <div class="wrap">
            <h1>Authentication Error</h1>
            <p>The following error(s) occurred:</p>
            <code>
                <?= $error ?> <br />
                <?= $error_description ?>
            </code>

            <p>Please contact an administrator for help, and quote: </p>
            <code>
                <?= str_replace("state=", "", $state) ?>
            </code>

            <p><a href="/">Return home?</a></p>
        </div>
    </body>
</html>