<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends Controller
{
    // Middleware untuk mengatur izin akses setiap method berdasarkan permission user
    function __construct()
    {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    /**
    * Menampilkan daftar role dengan pagination
    */
    public function index(Request $request)
    {
        $roles = Role::paginate(5);
        return view('admin.roles.index',compact('roles'));
    }
    
    /**
    * Menampilkan form untuk membuat role baru
    */
    public function create()
    {
        $permission = Permission::get(); // ambil semua permission
        return view('admin.roles.create',compact('permission'));
    }
    
    /**
    * Simpan role baru ke database
    */
    public function store(Request $request)
    {
        // Validasi input: nama role harus unik, permission harus diisi
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        // Buat role baru
        $role = Role::create(['name' => $request->input('name')]);
        // Set permission ke role tersebut
        $role->syncPermissions($request->input('permission'));

        // Logging aktivitas pembuatan role (audit trail)
        $role = new Role();
        activity()
            ->withProperties(['name' => $request->name])
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->log('You have created roles');
    
        return redirect()->route('admin.roles')
                        ->with('success','Role created successfully');
    }
    
    /**
    * Tampilkan form edit role beserta permission yang sudah dimiliki
    */
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        // Ambil permission yang sudah terkait dengan role ini
        $rolePermissions = DB::table("role_has_permissions")
        	->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();

        return view('admin.roles.edit',compact('role','permission','rolePermissions'));
    }
    
    /**
    * Update data role dan permission terkait di database
    */
    public function update(Request $request, $id)
    {
        // Validasi input wajib
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
    
        // Update permission role
        $role->syncPermissions($request->input('permission'));

        // Logging aktivitas update role
        $roles = new Role();
        activity()
            ->withProperties(['name' => $role->name])
            ->causedBy(auth()->user())
            ->performedOn($roles)
            ->log('You have edited roles');

        return redirect()->route('admin.roles')
                        ->with('success','Role updated successfully');
    }
    
    /**
    * Hapus role berdasarkan id
    */
    public function destroy($id)
    {
        $role = Role::find($id);
        $role_name = $role->name;

        $role->delete();

        // Logging aktivitas penghapusan role
        $role = new Role();
        activity()
            ->withProperties(['name' => $role_name])
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->log('You have deleted roles');

        return redirect()->route('admin.roles')
                        ->with('success','Role deleted successfully');
    }
}
