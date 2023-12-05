<?php

    $main_color = "blue";
    $info_color = "#a6ff44";
    $header_color = "dodgerblue";
    $default_card_border_color = "dodgerblue";

?>

.w3-card {
    border-color: <?=$default_card_border_color?>;
    border-style: solid;
    border-width: 1px;
}

.w3-card-4 {
    border-color: <?=$default_card_border_color?>;
    border-style: solid;
    border-width: 1px;
}

.info-card {
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
    border-bottom: solid 3px<?=$main_color?>;
}

textarea {
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
