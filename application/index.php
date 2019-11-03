<?php
require_once("../bootstrap.php");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Subscriber API</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='/assets/css/main.css'>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        var TOKEN = '<?= TOKEN ?>';

        $(document).ready(function() {
            $("#btnGo").click(function() {
                $('html, body').animate({
                    scrollTop: $("#body_content").offset().top
                }, 1000);
            });
            $(document).on('keypress', function(e) {
                if (e.which == 13) {
                    $('html, body').animate({
                        scrollTop: $("#body_content").offset().top
                    }, 1000);
                }
            });
        });
    </script>
</head>

<body>
    <div id="app">
        <div id="header_wrapper">
            <div id="header_content">
                <a href="#"><img src="/assets/images/logo.png" id="logo" /></a>
                <div id="header_title">Subscriber API</div>
                <div id="search">
                    <input type="text" placeholder="Search subscriber..." v-model="searchQuery" v-on:keyup.enter="getSubscribers" />
                    <button v-on:click="getSubscribers" id="btnGo">Go</button>
                </div>
            </div>

        </div>
        <div id="banner">
            <div id="banner_wrapper">
                <div id="subscribe_form">
                    <input type="text" placeholder="Enter Your Name" v-model="name" />
                    <input type="text" placeholder="Eneter Your Email" v-model="email" />
                    <button v-on:click="addSubscriber">Yes, Add Me!</button>
                    <div style="color:white; text-align:center;" v-html="message"></div>
                </div>
            </div>
        </div>
        <div id="body_content">
            <h1>MailerLite!</h1>
            <div style="margin-bottom: 20px;">Welcome to the home page. This application is <u>frameless</u>. If you are viewing this page, it means you config the site correctly. Yaahh!!!</div>
            <table-list :rows="rows" :columns="columns"></table-list>
            {{message2}}
        </div>
        <div id="footer">
            <div id="footer_content">
                PhiaThao@2019 &nbsp&nbsp | &nbsp&nbsp Subscriber API
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src='/assets/js/main.js'></script>
</body>

</html>