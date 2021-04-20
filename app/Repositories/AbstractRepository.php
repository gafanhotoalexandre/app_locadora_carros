<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function selectAtributosRegistrosRelacionados(string $atributos)
    {
        $this->model = $this->model->with($atributos);
        // a query está sendo montada
    }

    public function filtro(string $filtros)
    {
        $filtros = explode(';', $filtros);
        foreach ($filtros as $key => $condicao) {

            $c = explode(':', $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
            // a query está sendo montada
        }
    }

    public function selectAtributos(string $atributos)
    {
        $this->model = $this->model->selectRaw($atributos);
        // a query está sendo montada
    }

    public function getResultado()
    {
        return $this->model->get();
    }

}