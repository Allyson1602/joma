<?php
    use Slim\Http\Request;
    use Slim\Http\Response;
    use App\Models\Receita;
    use Illuminate\Database\Capsule\Manager as Capsule;

    $app->group('/receita', function(){
        $this->get('/{ref}', function(Request $request, Response $response){
            $receita = new Receita;

            $ref = $request->getAttribute('ref');
    
            $dados_receita = $receita
                ->select('receitas.*')
                ->where('titulo', '=', $ref)
                ->get();
            $dados_ingrediente = $receita
                ->join('ingredientes', 'receitas.id', '=', 'ingredientes.id_receita')
                ->select('ingredientes.ingrediente')
                ->where('titulo', '=', $ref)
                ->get();
            $dados_preparacoes = $receita
                ->join('preparacoes', 'receitas.id', '=', 'preparacoes.id_receita')
                ->select('preparacoes.preparacao')
                ->where('titulo', '=', $ref)
                ->get();
                

            $dados_receita = json_decode($dados_receita);
            $dados_receita = get_object_vars($dados_receita[0]);
            $dados_ingrediente = json_decode($dados_ingrediente);
            $dados_preparacoes = json_decode($dados_preparacoes);

            $ingredientes = array();
            for($i=0;$i<count($dados_ingrediente);$i++){
                array_push($ingredientes, $dados_ingrediente[$i]->ingrediente);
            }
            array_push($dados_receita, $ingredientes);
            
            $preparacoes = array();
            for($i=0;$i<count($dados_preparacoes);$i++){
                array_push($preparacoes, $dados_preparacoes[$i]->preparacao);
            }
            array_push($dados_receita, $preparacoes);
            
            return $response->withJson($dados_receita);
        });

        $this->post('/adiciona', function(Request $request, Response $response){
            $receita = new Receita();

            $db = $this->get('db');

            $db->table('receitas')->insert([
                'titulo' => 'Arroz doce',
                'subtitulo' => 'Arroz também pode ser doce.',
                'duracao' => '00:30:00',
                'dificuldade' => 'médio',
            ]);
            
            $ultimo_id = $receita->get('id')->last();

            $db->table('ingredientes')->insert([
                [ 'id_receita' => $ultimo_id['id'], 'ingrediente' => 'arroz' ],
                [ 'id_receita' => $ultimo_id['id'], 'ingrediente' => 'açúcar' ],
                [ 'id_receita' => $ultimo_id['id'], 'ingrediente' => 'leite' ],
                [ 'id_receita' => $ultimo_id['id'], 'ingrediente' => 'água' ],
            ]);
            $db->table('preparacoes')->insert([
                [ 'id_receita' => $ultimo_id['id'], 'preparacao' => 'Colocar o arroz com água até cobrir no fogo alto até ficar mole.' ],
                [ 'id_receita' => $ultimo_id['id'], 'preparacao' => 'Acrescenter açúcar e leite e deixar no fogo por 10 minutos.' ],
                [ 'id_receita' => $ultimo_id['id'], 'preparacao' => 'Se servir.' ],
            ]);
        });

        $this->put('/edita/{id_receita}', function(Request $request, Response $response){
            $receita = new Receita();
            $db = $this->get('db');

            // atualiza tabela receitas
            $db->table('receitas')
                ->where('id', $request->getAttribute('id_receita'))
                ->update(
                    ['titulo' => 'Arroz de doce'],
                    ['subtitulo' => 'Arroz também pode ser doce.'],
                    ['duracao' => '00:30:00'],
                    ['dificuldade' => 'médio'],
                );

            $id_receita = $db->table('receitas')
                ->where('id', $request->getAttribute('id_receita'))
                ->get('id')[0]->id;
            // atualiza tabela ingrediente
            $ingrediente = $db->table('ingredientes')
                ->select('ingrediente')
                ->where('id_receita', $request->getAttribute('id_receita'))
                ->get();
                $response->withJson($ingrediente);

            $db->table('ingredientes')
                ->where('id_receita', $request->getAttribute('id_receita'))
                ->delete();
                
            $db->table('ingredientes')->insert([
                [ 'id_receita' => $id_receita, 'ingrediente' => 'arroz' ],
                [ 'id_receita' => $id_receita, 'ingrediente' => 'açúcar' ],
                [ 'id_receita' => $id_receita, 'ingrediente' => 'leite' ],
                [ 'id_receita' => $id_receita, 'ingrediente' => 'canela' ],
            ]);

            // atualiza tabela preparações
            $preparacao = $db->table('preparacoes')
                ->select('preparacao')
                ->where('id_receita', $request->getAttribute('id_receita'))
                ->get();

                
            $response->withJson($preparacao);

            $db->table('preparacoes')
                ->where('id_receita', $request->getAttribute('id_receita'))
                ->delete();

            $db->table('preparacoes')->insert([
                [ 'id_receita' => $id_receita, 'preparacao' => 'Misturar o arroz com água até cobrir no fogo alto até ficar mole.' ],
                [ 'id_receita' => $id_receita, 'preparacao' => 'Adicionar açúcar e leite e deixar no fogo por 10 minutos.' ],
                [ 'id_receita' => $id_receita, 'preparacao' => 'Experimentar.' ],
            ]);
        });

        $this->delete('/remove/{id_receita}', function(Request $request, Response $response){
            $receita = new Receita();
            $db = $this->get('db');

            // deleta tabela ingredientes
            $db->table('ingredientes')
                ->where('id_receita', $request->getAttribute('id_receita'))
                ->delete();

            // deleta tabela preparações
            $db->table('preparacoes')
                ->where('id_receita', $request->getAttribute('id_receita'))
                ->delete();

            // deleta tabela receitas
            $db->table('receitas')
                ->where('id', $request->getAttribute('id_receita'))
                ->delete();
        });
    });
?>