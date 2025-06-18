<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class FileController extends Controller
{
    //

    public function myFiles() {
        return Inertia::render('MyFiles');
    }

    public function createFolder(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:files,id'
        ]);

        $parent = File::find($request->input('parent_id'));

        if (!$parent) {
            $parent = File::whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
        }

        $file = new File();
        $file->is_folder = 1;
        $file->name = $data['name'];

        $parent->appendNode($file);

        return back()->with('success', 'Folder created successfully!');
    }
   
}
