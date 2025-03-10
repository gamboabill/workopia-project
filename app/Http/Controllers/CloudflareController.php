<?php

namespace App\Http\Controllers;

use App\Models\Cloudflare;
use Illuminate\Http\Request;

class CloudflareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $url = Cloudflare::get()->first();
        return view('cf.index')->with('url', $url);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'url' => 'string|required|max:255'
        ]);

        Cloudflare::create($validatedData);

        return redirect()->route('cloudflare.index');
    }

    public function delete_cloudflare($id)
    {
        $cloudflare = Cloudflare::find($id);

        $cloudflare->delete();

        return redirect()->route('cloudflare.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
