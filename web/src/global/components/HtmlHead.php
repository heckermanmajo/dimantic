<?php

namespace src\global\components;

use cls\core\App;
use src\app\user\data\compositions\GetDarkmodeActive;
use src\core\Component;
use src\global\compositions\GetDevice;

readonly class HtmlHead extends Component {

  public function render(): void {
    ?>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- icon -->
    <link rel="icon" href="/res/gem.png" type="image/png">

    <!-- jquery -->
    <script
      src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- w3.css -->
    <link rel="stylesheet" href="/res/w3.css">

    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/ac3fc65406.js"
            crossorigin="anonymous"></script>

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"
            integrity="sha384-D1Kt99CQMDuVetoL1lrYwg5t+9QdHe7NLX/SoJYkXDFfX37iInKRy5xLSi8nO7UC"
            crossorigin="anonymous"></script>

    <style>
        body {
            background-color: darkslategray;
            color: white;
        }

        pre {
            white-space: pre-wrap; /* css-3 */
            white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
            /*white-space: -pre-wrap; /* Opera 4-6 */
            white-space: -o-pre-wrap; /* Opera 7 */
            word-wrap: break-word; /* Internet Explorer 5.5+ */
        }

        a {
            color: lightslategray;
            text-decoration: none;
        }

        body {
            font-family: "Open Sans", sans-serif;
        }

        body, textarea, input {
            color: #5b5b5b;
        }

        /** The markdown editor: EasyMDE */
        .CodeMirror {
            color: #5b5b5b;
        }

        .editor-statusbar {
            display: none !important;
        }

        button {
            background-color: #e1ecf4;
            border-radius: 3px;
            border: 1px solid #7aa7c7;
            box-shadow: rgba(255, 255, 255, .7) 0 1px 0 0 inset;
            box-sizing: border-box;
            color: #39739d;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system, system-ui, "Segoe UI", "Liberation Sans", sans-serif;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.15385;
            margin: 0;
            outline: none;
            padding: 8px .8em;
            position: relative;
            text-align: center;
            text-decoration: none;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: baseline;
            white-space: nowrap;
        }

        button:hover, button:focus {
            background-color: #b3d3ea;
            color: #2c5777;
        }

        button:focus {
            box-shadow: 0 0 0 4px rgba(0, 149, 255, .15);
        }

        button:active {
            background-color: #a0c7e4;
            box-shadow: none;
            color: #2c5777;
        }

        select {
            background-color: #e1ecf4;
            border-radius: 3px;
            border: 1px solid #bbc4cb;
            box-shadow: rgba(255, 255, 255, .7) 0 1px 0 0 inset;
            box-sizing: border-box;
            color: #3a4144;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system, system-ui, "Segoe UI", "Liberation Sans", sans-serif;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.15385;
            margin: 0;
            outline: none;
            padding: 8px .8em;
            position: relative;
            text-align: center;
            text-decoration: none;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            vertical-align: baseline;
            white-space: nowrap;
        }

        .red-button {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .red-button:hover, .red-button:focus {
            background-color: #f4c2c7;
            border-color: #e6676d;
            color: #721c24;
        }


        #overlay {
            position: fixed; /* Sit on top of the page content */
            display: none; /* Hidden by default */
            width: 100%; /* Full width (cover the whole page) */
            height: 100%; /* Full height (cover the whole page) */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
            z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
            cursor: pointer; /* Add a pointer on hover */
        }

        #overlay_content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fefefe;
            text-align: center;
            padding: 20px;
            z-index: 3;
            border-radius: 5px;
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
        }

        .selected-link-color{
            color: dodgerblue;
        }

        <?php if(GetDarkmodeActive::is_active()): ?>

        body {
            background-color: #333;
            color: #ffffff;
        }

        a{
            color: #ff9a9a;
        }

        .selected-link-color{
            color: #e400ff;
        }

        button {
            background-color: #656565;
            border-radius: 3px;
            border: none;
            box-shadow: none;
            color: #ffffff;
        }

        button:hover, button:focus {
            background-color: #ff9a9a;
            color: #2c5777;
        }

        button:focus {
            box-shadow: 0 0 0 4px rgba(0, 149, 255, .15);
        }

        button:active {
            background-color: #ff9a9a;
            box-shadow: none;
            color: #612020;
        }

        select {
            background-color: #656565;
            border-radius: 3px;
            border-style: none;
            box-shadow: none;
            color: #ddd;
        }

        input {
            background-color: #656565;
            border-radius: 3px;
            border-style: none;
            box-shadow: none;
            color: #ddd;
        }


        .CodeMirror {
            color: #ddd;
            background-color: #656565;
        }


        <?php endif; ?>

        <?php if(GetDevice::is_mobile()): ?>

        body {
            font-size: 14px;
            background-color: red;
        }

        <?php endif; ?>
    </style>

    <?php
  }

}