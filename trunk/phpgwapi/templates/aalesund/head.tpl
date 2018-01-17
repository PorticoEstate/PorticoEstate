<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
                <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >


        <!-- BEGIN stylesheet -->
        <link href="{stylesheet_uri}" type="text/css" rel="StyleSheet">
        <!-- END stylesheet -->
        <!-- BEGIN javascript -->
        <script type="text/javascript" src="{javascript_uri}"></script>
        <!-- END javascript -->
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />
       
        {css}

        <script type="text/javascript">
            <!--
                var strBaseURL = '{str_base_url}';
            //-->
        </script>
        
        {javascript}
        <script type="text/javascript" src="{samplejs}"></script>
        <script type="text/javascript" src="{bootstrapjs}"></script>
        <script type="text/javascript" src="{bootstrapmainjs}"></script>

        <script type="text/javascript">
        <!--
            {win_on_events}
            //-->
        </script>

    </head>
    <body class="">
        <nav class="navbar navbar-expand-md">

                <div class="container header-container">
                    <button class="navbar-toggler mr-auto active" type="button" data-toggle="collapse" data-target="#Navbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>  
                    <a class="navbar-brand" href="{site_url}">
                        <img src="{logoimg}" alt="Logo" style="height: 100px;">
                    </a>
                    <div class="collapse navbar-collapse text-center" id="Navbar">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item active"><a class="nav-link" href="{site_url}">Hjem</a></li>
   <!--                         <li class="nav-item active"><a class="nav-link" href="#">Søk</a></li>
                            <li class="nav-item active"><a class="nav-link" href="#">Lokaler</a></li> -->
                            <li class="nav-item active"><a class="nav-link" href="{manual_url}">{manual_text}</a></li>
                            <li class="nav-item active"><a id="login" class="nav-link" href="{login_url}">{login_text}</a><span id="change"></span></li>
                        </ul>   
                    </div>         
                </div>

            </nav>

