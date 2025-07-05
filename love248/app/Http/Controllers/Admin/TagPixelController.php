<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TagPixel;
use Auth;

class TagPixelController extends Controller
{
    public function index(Request $request){
        $tagPixel = TagPixel::latest()->get();
        return view('admin.TagPixel.index',compact('tagPixel'));
    }

    public function create(Request $request){
        return view('admin.TagPixel.create');
    }
    public function store(Request $request){
        $validated = $request->validate([
            'type' => 'required',
            'code' => 'required',
        ]);

        $user = Auth::user();
        $data = [
            'user_id' => $user->id ?? '',
            'type' => $request->type ?? '',
            'code' => $request->code ?? '',
        ];
        TagPixel::create($data);

        return redirect()->route('admin.tag-pixels.index')->with('msg', 'Tag pixel add .');
    }
    public function edit($id){
        $tagPixel = TagPixel::findOrFail($id);
        return view('admin.TagPixel.edit',compact('tagPixel'));
    }
    public function update(Request $request){
        $validated = $request->validate([
            'type' => 'required',
            'code' => 'required',
        ]);

        $data = [
            'type' => $request->type ?? '',
            'code' => $request->code ?? '',
        ];
        TagPixel::where('id',$request->id ?? '')->update($data);
        return redirect()->route('admin.tag-pixels.index')->with('msg', 'Tag pixel update .');
    }
    public function destory($id){
        $tagPixel = TagPixel::findOrFail($id);
        if($tagPixel){
            $tagPixel->delete();
        }
        return back()->with('msg', 'Successfully removed Tag Pixel');
    }
}
