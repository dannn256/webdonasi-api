<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;
use App\Http\Requests\SettingRequest;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Menampilkan halaman dashboard beserta 5 aktivitas terbaru user yang login
    public function index()
    {
        $logs = Activity::where('causer_id', auth()->id())->latest()->paginate(5);

        return view('admin.dashboard', compact('logs'));
    }

    // Menampilkan semua log aktivitas user dengan pagination 10 data per halaman
    public function activity_logs()
    {
        $logs = Activity::where('causer_id', auth()->id())->latest()->paginate(10);
        return view('admin.logs', compact('logs'));
    }

    // Simpan pengaturan situs yang diinput admin, termasuk upload logo
    public function settings_store(SettingRequest $request)
    {
        // Jika ada file logo, simpan file dan set path di setting
        if($request->file('logo')) {
            $filename = $request->file('logo')->getClientOriginalName();
            $filePath = $request->file('logo')->storeAs('uploads', $filename, 'public');
            setting()->set('logo', $filePath);
        }

        // Simpan data setting lainnya
        setting()->set('site_name', $request->site_name);
        setting()->set('keyword', $request->keyword);
        setting()->set('description', $request->description);
        setting()->set('url', $request->url);

        // Simpan semua setting ke database
        setting()->save();

        return redirect()->back()->with('success', 'Settings has been successfully saved');
    }

    // Update data profil user, termasuk nama, password, dan avatar
    public function profile_update(Request $request)
    {
        $data = ['name' => $request->name];

        // Jika ingin ganti password, cek dulu password lama benar atau tidak
        if($request->old_password && $request->new_password) {
            if(!Hash::check($request->old_password, auth()->user()->password)) {
                session()->flash('failed', 'Password is wrong!');
                return redirect()->back();
            }

            // Jika benar, simpan password baru dengan hash
            $data['password'] = Hash::make($request->new_password);
        } 

        // Jika ada avatar baru, simpan dan hapus avatar lama jika ada
        if($request->avatar) {
            $data['avatar'] = $request->avatar;

            if(auth()->user()->avatar) {
                unlink(storage_path('app/public/'.auth()->user()->avatar));
            }
        }
        
        // Update data user di database
        auth()->user()->update($data);
        
        return redirect()->back()->with('success', 'Profile updated!');
    }

    // Proses upload file avatar, simpan ke folder khusus user, dan kembalikan path filenya
    public function upload_avatar(Request $request)
    {
        $request->validate(['avatar'  => 'file|image|mimes:jpg,png,svg|max:1024']);

        if($request->hasFile('avatar')){
            $file = $request->file('avatar');

            $fileName = $file->getClientOriginalName();
            $folder = 'user-'.auth()->id();

            $file->storeAs('avatars/'.$folder, $fileName, 'public');

            return 'avatars/'.$folder.'/'.$fileName;
        }

        return '';
    }

    // Hapus semua log aktivitas yang berumur lebih dari satu minggu
    public function delete_logs()
    {
        $logs = Activity::where('created_at', '<=', Carbon::now()->subWeeks())->delete();

        return back()->with('success', $logs.' Logs successfully deleted!');
    }
}
