<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pokedex</title>

    <link rel="stylesheet" href="../../css/style.css">

    <link rel="shortcut icon" href="../../img/Pokedex_tool_icon-icons.com_67529.ico">

    <style>

    .pokemon-hover-card 
    {
        position: absolute; 
        top: -50px; 
        left: calc(100% + 10px); 
        width: 300px;
        
        background-color: #444; 
        color: #fff;
        border: 1px solid #555;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        
        visibility: hidden;
        opacity: 0;
        transition: visibility 0s, opacity 0.3s linear;

        position: absolute; 
        top: -50px; 
        left: calc(100% + 10px); /* A posição está relativa à borda direita da célula */
        /* ... */
        z-index: 9999;
    }

    
    .pokemon-hover-container 
    {
        position: relative;
        cursor: pointer;
    }

    .pokemon-hover-container:hover .pokemon-hover-card 
    {
        visibility: visible;
        opacity: 1;
        display: block;
        
    }

    .card-image 
    {
        max-width: 100px;
        height: auto;
        display: block;
        margin: 0 auto 10px;
        background-color: #333;
        border-radius: 50%;
    }

    .card-details p 
    {
        margin-bottom: 3px;
        font-size: 0.9em;
    }
    </style>

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous"> -->
</head>

<body>

    <div class="container mt-5">
