<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    /** 
     * Cria um atributo baseado no objeto injetado
     * 
     * @param \App\Models\Marca $marca
    */
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $marcas = Marca::all();
        $marcas = $this->marca->with('modelos')->get();
        return response()->json($marcas, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $marca = Marca::create($request->all());
        // nome
        // imagem

        $request->validate($this->marca->rules(), $this->marca->feedback());
        // stateless => se houver um parâmetro inválido, retornar json informando o ocorrido

        
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        /* -- Outra Opção --
        $marca->nome = $request->nome;
        $marca->imagem = $imagem_urn;
        $marca->save();
        */
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if ($marca === null) {
            return response()->json([
                'erro' => 'Recurso pesquisado não existe'
            ], 404);// segundo parâmetro => Status Code
        }
        return response()->json($marca, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        // $marca->update($request->all());
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json([
                'erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'
            ], 404);// segundo parâmetro => Status Code
        }

        if ($request->method() === 'PATCH') {
            
            $dinamicRules = array();

            // percorrendo todas as regras definidas no Model
            foreach ($marca->rules() as $input => $rule) {

                // coletar apenas as regras aplicáveis aos parâmetros parciais de requisição PATCH
                if (array_key_exists($input, $request->all())) {
                    $dinamicRules[$input] = $rule;
                }
            }
            $request->validate($dinamicRules, $marca->feedback());

        } else { // PUT
            $request->validate($marca->rules(), $marca->feedback());
        }

        // remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        // e salva a nova imagem retornando o caminho para $imagem_urn
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens', 'public');    
        }


        // preencher o objeto $marca com os dados do request
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn ?? $marca->imagem;
        // método save() atualiza se existir um id, ou cria um novo caso não tenha
        $marca->save();

        // $marca->update([
        //     'nome' => $request->nome,
        //     'imagem' => $imagem_urn
        // ]);
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json([
                'erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'
            ], 404);// segundo parâmetro => Status Code
        }

        // remove o arquivo no storage
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json([
            'msg' => 'A marca foi removida com sucesso!'
        ], 200);
    }
}
