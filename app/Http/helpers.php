<?php

function app_name()
{
    return config('app.name');
}

function css($path)
{
    return asset("css/$path.css");
}

function js($path)
{
    return asset("js/$path.js");
}

function images($path)
{
    return asset("images/$path");
}

function png($path)
{
    return images("$path.png");
}

function jpg($path)
{
    return images("$path.jpg");
}