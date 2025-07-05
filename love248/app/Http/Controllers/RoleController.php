<?php

    

namespace App\Http\Controllers;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\RedirectResponse;    
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use DB;

class RoleController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    function __construct()

    {

        //  $this->middleware('permission:rol|role-create|role-edit|role-delete', ['only' => ['index','store']]);

        //  $this->middleware('permission:role-create', ['only' => ['create','store']]);

        //  $this->middleware('permission:role-edit', ['only' => ['edit','update']]);

        //  $this->middleware('permission:role-delete', ['only' => ['destroy']]);

    }

    

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('role-list')) {
            return redirect('/admin')->with('msg', 'You do not have for this route !');
        }
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('roles.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);

    }

    

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('role-create')) {
            return redirect('/admin')->with('msg', 'You do not have for this route !');
        }
        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }

    

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('role-create')) {
            return redirect('/admin')->with('msg', 'You do not have for this route !');
        }
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ],[
            'name.unique'=>'Name should be unique!',
            'permission'=>'Please select permission!',
        ]);
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        return redirect()->route('roles.index')->with('success','Role created successfully');
    }

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('role-edit')) {
            return redirect('/admin')->with('msg', 'You do not have for this route !');
        }

        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('roles.show',compact('role','rolePermissions'));
    }

    

    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id): View
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('role-edit')) {
            return redirect('/admin')->with('msg', 'You do not have for this route !');
        }
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('roles.edit',compact('role','permission','rolePermissions'));

    }

    

    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id): RedirectResponse{
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('role-edit')) {
            return redirect('/admin')->with('msg', 'You do not have for this route !');
        }

        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');

    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id): RedirectResponse
    {
        $user = Auth::user();
        if ($user->hasRole('subadmin') && !$user->can('role-delete')) {
            return redirect('/admin')->with('msg', 'You do not have for this route !');
        }

        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');

    }

}
?>