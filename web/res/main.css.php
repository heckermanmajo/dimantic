<?php


use cls\App;

try{

    $style = App::get()->get_currently_logged_in_account()->style;

    switch($style) {

        default:
            $main_color = "blue";
            $info_color = "#a6ff44";
            $header_color = "dodgerblue";
            $default_card_border_color = "dodgerblue";
            $background_color = "gray";
            $card_background_color = "gray";
            break;

        case "default_darkmode" :
            $main_color = "white";
            $info_color = "#a6ff44";
            $header_color = "dodgerblue";
            $default_card_border_color = "dodgerblue";
            $background_color = "#232323";
            $card_background_color = "gray";
            break;

    }

}catch(Exception $e){
    // this is the style for not logged in users
    $background_color = "gray";
    $card_background_color = "gray";
    $main_color = "blue";
    $info_color = "#a6ff44";
    $header_color = "dodgerblue";
    $default_card_border_color = "dodgerblue";

}

?>

body {
    background-color: <?=$background_color?>;
}

.w3-card {
    background-color: <?=$card_background_color?>;
    border-color: <?=$default_card_border_color?>;
    border-style: solid;
    border-width: 1px;
}

.w3-card-4 {
    background-color: <?=$card_background_color?>;
    border-color: <?=$default_card_border_color?>;
    border-style: solid;
    border-width: 1px;
}

.info-card {
    background-color: <?=$card_background_color?>;
    border-style: solid;
    border-width: 1px;
    border-color: <?=$info_color?>;
    border-left-width: 15px;
    padding: 10px;
    margin: 16px;
}

input {
    background-color: #ffffff;
    border-style: none;
    border-bottom: solid 1px<?=$main_color?>;
    color: #0c0c0c;
}

input:focus {
    outline: none;
}

textarea {
    background-color: <?=$card_background_color?> !important;
    border-left-style: none;
    border-right-style: none;
    border-bottom: solid 1px<?=$main_color?>;
    border-top: solid 1px<?=$main_color?>;
    color: #252525;
}

.button {
    border-color: <?=$main_color?>;
    border-style: solid;
    border-width: 1px;
    padding: 4px 8px 4px 8px;
    color: <?=$main_color?>;
    background-color: inherit;
    text-decoration: none;
    cursor: pointer;
}

.delete-button {
    border-color: #ff0000;
    border-style: solid;
    border-width: 1px;
    /*padding: 4px 8px 4px 8px;*/
    color: #ff0000;
    background-color: inherit;
    text-decoration: none;
    cursor: pointer;
}

.delete-button:hover {
    color: #232323 !important;
    border-color: #643f3f !important;
}

.button:hover {
    color: #000000 !important;
    border-color: #3f3d3d !important;
}

pre {
    /* line break */
    white-space: pre-wrap !important;
}

.quote {
    font-size: 90%;
    border-left: solid 5px<?=$default_card_border_color?>;
    padding-left: 10px;
    font-style: italic;
}

blockquote {
    border-left: solid 3px #818181;
    padding-left: 7px;
    color: #818181 !important;
    font-style: italic;
}

.menu-header-color {
    padding-left: 10px;
    font-style: italic;
    color: <?=$header_color?>;
}


/* CSS */
.sketch-button {
    align-self: center;
    background-color: <?=$card_background_color?>;
    background-position: 0 90%;
    background-repeat: repeat no-repeat;
    background-size: 4px 3px;
    border-radius: 15px 225px 255px 15px 15px 255px 225px 15px;
    border-style: solid;
    border-width: 2px;
    box-shadow: rgba(0, 0, 0, .2) 15px 28px 25px -18px;
    box-sizing: border-box;
    color: #0048ff;
    cursor: pointer;
    display: inline-block;
    font-family: Neucha, sans-serif;
    font-size: 1rem;
    line-height: 23px;
    outline: none;
    padding: .75rem;
    text-decoration: none;
    transition: all 235ms ease-in-out;
    border-bottom-left-radius: 15px 255px;
    border-bottom-right-radius: 225px 15px;
    border-top-left-radius: 255px 15px;
    border-top-right-radius: 15px 225px;
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;


    font-weight: bold;
}


.sketch-card {
    background-color: <?=$card_background_color?>;
    align-self: center;
    background-image: none;
    background-position: 0 90%;
    background-repeat: repeat no-repeat;
    background-size: 4px 3px;
    border-radius: 15px 225px 255px 15px 15px 255px 225px 15px;
    border-style: solid;
    border-width: 2px;
    box-shadow: rgba(0, 0, 0, .2) 15px 28px 25px -18px;
    box-sizing: border-box;
    color: #41403e;
    /*cursor: pointer;*/
    /*display: inline-block;*/
    font-family: Neucha, sans-serif;
    font-size: 1rem;
    line-height: 23px;
    outline: none;
    padding: .75rem;
    text-decoration: none;
    transition: all 235ms ease-in-out;
    border-bottom-left-radius: 15px 255px;
    border-bottom-right-radius: 225px 15px;
    border-top-left-radius: 255px 15px;
    border-top-right-radius: 15px 225px;
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
}

.sketch-button:hover {
    box-shadow: rgba(0, 0, 0, .3) 2px 8px 8px -5px;
    transform: translate3d(0, 2px, 0);
}

.sketch-button:focus {
    box-shadow: rgba(0, 0, 0, .3) 2px 8px 4px -6px;
}