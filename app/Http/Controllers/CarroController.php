<?php

namespace App\Http\Controllers;

use App\Repositories\CarroRepository;
use App\Models\Carro;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    /** 
     * Cria um atributo baseado no objeto injetado
     * 
     * @param \App\Models\Carro $carro
    */
    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);

        if ($request->has('atributos_modelo')) {
            $atributos_modelo = 'modelo:id,'. $request->atributos_modelo;
            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if ($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $carroRepository->selectAtributos($request->atributos);
        }
        
        return response()->json($carroRepository->getResultado(), 200);

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
        $request->validate($this->carro->rules());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        /* -- Outra Opção --
        $marca->nome = $request->nome;
        $marca->imagem = $imagem_urn;
        $marca->save();
        */
        return response()->json($carro, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $carro = $this->carro->with('modelo')->find($id);
        if ($carro === null) {
            return response()->json([
                'erro' => 'Recurso pesquisado não existe'
            ], 404);// segundo parâmetro => Status Code
        }
        return response()->json($carro, 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function edit(Carro $carro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json([
                'erro' => 'Impossível realizar a atualização. O recurso solicitado não existe'
            ], 404);// segundo parâmetro => Status Code
        }

        if ($request->method() === 'PATCH') {
            
            $dinamicRules = array();

            // percorrendo todas as regras definidas no Model
            foreach ($carro->rules() as $input => $rule) {

                // coletar apenas as regras aplicáveis aos parâmetros parciais de requisição PATCH
                if (array_key_exists($input, $request->all())) {
                    $dinamicRules[$input] = $rule;
                }
            }
            $request->validate($dinamicRules);

        } else { // PUT
            $request->validate($carro->rules());
        }


        // preencher o objeto $marca com os dados do request
        $carro->fill($request->all());
        $carro->save();
        return response()->json($carro, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json([
                'erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'
            ], 404);// segundo parâmetro => Status Code
        }

        $carro->delete();
        return response()->json([
            'msg' => 'O carro foi removido com sucesso!'
        ], 200);

    }
}
