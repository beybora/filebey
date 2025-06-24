<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    private function getRoot()
    {
        return File::whereIsRoot()
            ->where('created_by', Auth::id())
            ->firstOrFail();
    }
    public function myFiles()
    {
        $folder =  $this->getRoot();

        $items = File::query()
            ->where('parent_id', $folder->id)
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('MyFiles', [
            'items' => $items,
        ]);
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
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:files,id',
        ]);

        $file = File::with('descendants')->where('id', $validated['id'])->firstOrFail();

        if ($file->created_by !== Auth::id()) {
            abort(403, 'You do not have permission to delete this item.');
        }

        $file->delete();

        return back()->with('success', 'Deleted successfully!');
    }


}
