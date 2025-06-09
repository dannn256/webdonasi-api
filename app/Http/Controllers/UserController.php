<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MemberRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    // Middleware untuk mengatur izin akses sesuai permission user
    function __construct()
    {
        $this->middleware('permission:member-list|member-create|member-edit|member-delete', ['only' => ['index','store']]);
        $this->middleware('permission:member-create', ['only' => ['create','store']]);
        $this->middleware('permission:member-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:member-delete', ['only' => ['destroy']]);
    }

    /**
     * Tampilkan daftar user dengan pagination
     */
    public function index(Request $request)
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index',compact('users'));
    }

    /**
     * Tampilkan form untuk menambah user baru
     */
    public function create()
    {
        // Ambil daftar role (nama) untuk dropdown role di form
        $roles = Role::pluck('name','name')->all();
        return view('admin.users.create',compact('roles'));
    }

    /**
     * Simpan user baru ke database
     */
    public function store(MemberRequest $request)
    {
        $input = $request->all();

        // Hash password sebelum disimpan
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        // Assign role yang dipilih ke user baru
        $user->assignRole($request->input('roles'));

        return redirect()->back()
                         ->with('success','User created successfully');
    }

    /**
     * Tampilkan form edit user beserta role yang dimiliki
     */
    public function edit($id)
    {
        $user = User::find($id);

        // Ambil semua role yang ada
        $roles = Role::pluck('name','name')->all();

        // Ambil role yang sudah dimiliki user ini
        $userRole = $user->roles->pluck('name','name')->all();

        return view('admin.users.edit',compact('user','roles','userRole'));
    }

    /**
     * Update data user
     */
    public function update(Request $request, $id)
    {
        // Validasi input user
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'same:confirm-password',  // pastikan password sama dengan konfirmasi
            'roles' => 'required'
        ]);

        $input = $request->all();

        // Jika password diisi, hash dulu, kalau tidak hapus dari input supaya tidak diupdate
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        $user = User::find($id);
        $user->update($input);

        // Hapus semua role lama user agar bisa diset ulang
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        // Assign ulang role yang dipilih
        $user->assignRole($request->input('roles'));

        return redirect()->route('admin.member')
                         ->with('success','User updated successfully');
    }

    /**
     * Hapus user berdasarkan id
     */
    public function destroy($id)
    {
        User::find($id)->delete();

        return redirect()->route('admin.member')
                         ->with('success','User deleted successfully');
    }
}
