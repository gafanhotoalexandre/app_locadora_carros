<?php

namespace App\Http\Controllers;

use App\Repositories\ModeloRepository;
use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    /**
     * Injetando dependĂȘncia 
     * 
     * @param App\Models\Modelo
     */
    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $modeloRepository = new ModeloRepository($this->modelo);

        if ($request->has('atributos_marca')) {
            $atributos_marca = 'marca:id,'. $request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $modeloRepository->selectAtributos($request->atributos);
        }
        
        return response()->json($modeloRepository->getResultado(), 200);

        // $modelos = array();

        // if ($request->has('atributos_marca')) {
        //     $atributos_marca = $request->atributos_marca;
        //     $modelos = $this->modelo->with('marca:id,'. $atributos_marca);
        // } else {
        //     $modelos = $this->modelo->with('marca');
        // }

        // if ($request->has('filtro')) {
        //     $filtros = explode(';', $request->filtro);

        //     foreach($filtros as $key => $condicao) {

        //         $c = explode(':', $condicao);
        //         $modelos = $modelos->where($c[0], $c[1], $c[2]);
        //     //                            (<valor>, <operador>, <valor>) 
        //     }
        // }

        // if ($request->has('atributos')){ // verificando se parĂąmetro existe
        //     $atributos = $request->atributos;
        //     $modelos = $modelos->selectRaw($atributos)->get();
        // } else {
        //     $modelos = $modelos->get();
        // }
        // // $modelos = $this->modelo->with('marca')->get();
        // return response()->json($modelos, 200);
        // all() -> criando um obj de consulta + get() = collection
        // get() -> possibilidade de modificar a consulta -> collection
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $modelo
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        if ($modelo === null) {
            return response()->json([
                'erro' => 'Recurso pesquisado nĂŁo existe'
            ], 404);
        }

        return response()->json($modelo, 200);
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
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json([
                'erro' => 'ImpossĂ­vel realizar a atualizaĂ§ĂŁo. O recurso solicitado nĂŁo existe'
            ], 404);// segundo parĂąmetro => Status Code
        }

        if ($request->method() === 'PATCH') {
            
            $dinamicRules = array();

            // percorrendo todas as regras definidas no Model
            foreach ($modelo->rules() as $input => $rule) {

                // coletar apenas as regras aplicĂĄveis aos parĂąmetros parciais de requisiĂ§ĂŁo PATCH
                if (array_key_exists($input, $request->all())) {
                    $dinamicRules[$input] = $rule;
                }
            }
            $request->validate($dinamicRules);

        } else { // PUT
            $request->validate($modelo->rules());
        }

        // remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        // e salva a nova imagem retornando o caminho para $imagem_urn
        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);

            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/modelos', 'public');
        }

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn ?? $modelo->imagem;
        $modelo->save();
        /*
        $modelo->update([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);
        */
        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json([
                'erro' => 'ImpossĂ­vel realizar a exclusĂŁo. O recurso solicitado nĂŁo existe'
            ], 404);
        }

        // removendo o arquivo demarcado
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json([
            'msg' => 'O modelo foi removida com sucesso!'
        ], 200);
    }
}
