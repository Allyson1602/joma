<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Receita extends Model{
        protected $fillable = [
            'titulo', 'subtitulo', 'ingredientes', 'duracao', 'preparacao', 'dificuldade'
        ];
    }
?>