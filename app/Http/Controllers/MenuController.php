<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Get all menu items (Public).
     */
    public function index()
    {
        return response()->json(\App\Models\Menu::all(), 200);
    }

    /**
     * Get detailed menu item (Public).
     */
    public function show($id)
    {
        $menu = \App\Models\Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }
        return response()->json($menu, 200);
    }

    /**
     * Create new menu item (Auth).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:5120', // Max 5MB
        ]);

        $data = $request->only(['name', 'description', 'price']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            // Store in storage/app/public/uploads/menu
            $path = $file->storeAs('uploads/menu', $filename, 'public');
            // Save relative path or full URL. Requirement says "URL/path".
            // I'll save the path relative to storage root for easier deleting.
            // On response, accessor can be used, or just append storage url.
            $data['image'] = 'storage/' . $path;
        }

        $menu = \App\Models\Menu::create($data);

        return response()->json($menu, 201);
    }

    /**
     * Update menu item (Auth).
     */
    public function update(Request $request, $id)
    {
        $menu = \App\Models\Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['name', 'description', 'price']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menu->image && file_exists(public_path($menu->image))) {
                unlink(public_path($menu->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/menu', $filename, 'public');
            $data['image'] = 'storage/' . $path;
        }

        $menu->update($data);

        return response()->json($menu, 200);
    }

    /**
     * Delete menu item (Auth).
     */
    public function destroy($id)
    {
        $menu = \App\Models\Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        if ($menu->image && file_exists(public_path($menu->image))) {
            unlink(public_path($menu->image));
        }

        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully'], 200);
    }
}
