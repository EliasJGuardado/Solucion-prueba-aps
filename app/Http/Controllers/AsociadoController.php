<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Asociado;

class AsociadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $asociados = Asociado::orderBy('id','DESC')->get();
        return $asociados;
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
        $asociados = new Asociados();
        $asociados->nombre = $request->nombre;
        $asociados->dui = $request->dui;
        $asociados->nit = $request->nit;
        $asociados->direccion = $request->direccion;
        $asociados->estado = '1';
        $asociados->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request)
    {
        $asociados = Asociado::findOrFail($request->id);
        $asociados->nombre = $request->nombre;
        $asociados->dui = $request->dui;
        $asociados->nit = $request->dui;
        $asociados->descripcion = $request->descripcion;
        $asociados->estado = '1';
        $asociados->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function desactivar(Request $request)
    {
        //metodo para desactivar este es el modelo
        $asociados = Asociado::findOrFail($request->id);
        $asociados->estado = '0';
        $asociados->save();
    }
    public function activar(Request $request)
    {
          //metodo para activar este es el modelo
          $asociados = Asociado::findOrFail($request->id);
          $asociados->estado = '1';
          $asociados->save();
    }
}
