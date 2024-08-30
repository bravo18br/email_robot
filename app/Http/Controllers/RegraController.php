<?php

namespace App\Http\Controllers;

use App\Models\Regra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegraController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $this->validaRegra($request);
        if ($validatedData instanceof \Illuminate\Http\RedirectResponse) {
            return $validatedData;
        }
        Regra::create($validatedData);
        return redirect()->back()->with(['sucesso' => 'Regra criada com sucesso!']);
    }

    public function update(Request $request, Regra $regra)
    {
        $validatedData = $this->validaRegra($request);
        if ($validatedData instanceof \Illuminate\Http\RedirectResponse) {
            return $validatedData;
        }
        $regra->update($validatedData);
        return redirect()->back()->with(['sucesso' => 'Regra atualizada com sucesso!']);
    }

    public function destroy(Regra $regra)
    {
        $regra->delete();
        return redirect()->back()->with(['sucesso' => 'Regra excluída com sucesso!']);
    }

    public function validaRegra(Request $request)
    {
        $dadosRequest = $request->all();
        $validatedData = Validator::make($dadosRequest, [
            'descricao' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'webhook' => 'required|url|max:255',
            'status' => 'required|string|max:255',
        ]);

        if ($validatedData->fails()) {
            // Redireciona com erros de validação
            return redirect()->back()->withErrors($validatedData)->withInput()->with(['erro' => 'Ação não executada']);
        }

        // Retorna dados validados se não houver erros
        return $validatedData->validated();
    }
}
