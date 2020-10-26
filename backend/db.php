<?php
    // bando de dados criado

    if(PHP_SAPI != 'cli'){
        exit('rodar via CLI');
    }

    require __DIR__.'/vendor/autoload.php';

    // instanciando app
    $settings = require __DIR__.'/src/settings.php';
    $app = new \Slim\App($settings);

    // set up dependencies
    require __DIR__.'/src/dependencies.php';

    $db = $container->get('db');

    $schema = $db->schema();
    $tabela = 'receitas';

    $schema->dropIfExists($tabela);

    $schema->create($tabela, function($table){
        $table->increments('id');
        $table->string('titulo', 255);
        $table->string('subtitulo', 255);
        $table->text('preparacao');
        $table->time('duracao');
        $table->string('dificuldade');
    });
?>