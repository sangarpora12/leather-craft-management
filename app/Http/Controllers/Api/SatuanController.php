<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        return Satuan::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan',
        ]);

        return Satuan::create($request->all());
    }

    public function show($id)
    {
        return Satuan::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->update($request->all());
        return $satuan;
    }

    public function destroy($id)
    {
        Satuan::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}