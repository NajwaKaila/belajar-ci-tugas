<?php

namespace App\Controllers;

use App\Models\DiskonModel;

class DiskonController extends BaseController
{
    public function index()
    {
        if (session('role') !== 'admin') return redirect()->to('/');
        $model = new DiskonModel();
        $data['diskon'] = $model->orderBy('tanggal', 'ASC')->findAll();
        return view('diskon/index', $data);
    }

    public function create()
    {
        if (session('role') !== 'admin') return redirect()->to('/');
        return view('diskon/create');
    }

    public function store()
    {
        if (session('role') !== 'admin') return redirect()->to('/');
        $model = new DiskonModel();

        $rules = [
            'tanggal' => 'required|is_unique[diskon.tanggal]',
            'nominal' => 'required|numeric'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->save([
            'tanggal' => $this->request->getPost('tanggal'),
            'nominal' => $this->request->getPost('nominal'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/diskon')->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (session('role') !== 'admin') return redirect()->to('/');
        $model = new DiskonModel();
        $data['diskon'] = $model->find($id);
        return view('diskon/edit', $data);
    }

    public function update($id)
    {
        if (session('role') !== 'admin') return redirect()->to('/');
        $model = new DiskonModel();

        $model->update($id, [
            'nominal' => $this->request->getPost('nominal'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/diskon')->with('success', 'Diskon berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') return redirect()->to('/');
        $model = new DiskonModel();
        $model->delete($id);
        return redirect()->to('/diskon')->with('success', 'Diskon berhasil dihapus.');
    }
}
