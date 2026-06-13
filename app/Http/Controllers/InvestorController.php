<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvestorController extends Controller
{
    public function index()
    {
        abort_if(Auth::user()->role !== 'superadmin', 403, 'Akses Ditolak: Hanya Superadmin yang dapat mengakses menu Investor.');

        $investors = Investor::with('user')->orderBy('name', 'asc')->paginate(10);
        return view('investors.index', compact('investors'));
    }

    public function create()
    {
        abort_if(Auth::user()->role !== 'superadmin', 403, 'Akses Ditolak: Hanya Superadmin.');

        $unlinkedUsers = User::where('role', 'investor')
            ->whereDoesntHave('investor')
            ->get();

        return view('investors.create', compact('unlinkedUsers'));
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role !== 'superadmin', 403);

        $request->validate([
            'account_type' => 'required|in:new,existing',
            'name' => 'required|string|max:255',
            'share_percentage' => 'required|numeric|min:0|max:100',
            // If new user
            'email' => 'required_if:account_type,new|nullable|email|unique:users,email',
            'password' => 'required_if:account_type,new|nullable|string|min:8',
            // If existing user
            'user_id' => 'required_if:account_type,existing|nullable|exists:users,id|unique:investors,user_id',
        ]);

        if ($request->account_type === 'new') {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'investor',
            ]);
            $userId = $user->id;
        } else {
            $userId = $request->user_id;
        }

        Investor::create([
            'user_id' => $userId,
            'name' => $request->name,
            'share_percentage' => $request->share_percentage,
        ]);

        return redirect()->route('investors.index')->with('success', 'Investor berhasil ditambahkan.');
    }

    public function edit(Investor $investor)
    {
        abort_if(Auth::user()->role !== 'superadmin', 403);

        return view('investors.edit', compact('investor'));
    }

    public function update(Request $request, Investor $investor)
    {
        abort_if(Auth::user()->role !== 'superadmin', 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'share_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $investor->update([
            'name' => $request->name,
            'share_percentage' => $request->share_percentage,
        ]);

        // Also update name of the user if linked
        if ($investor->user) {
            $investor->user->update([
                'name' => $request->name,
            ]);
        }

        return redirect()->route('investors.index')->with('success', 'Investor berhasil diperbarui.');
    }

    public function destroy(Investor $investor)
    {
        abort_if(Auth::user()->role !== 'superadmin', 403);

        $user = $investor->user;
        $investor->delete();
        
        if ($user) {
            $user->delete();
        }

        return redirect()->route('investors.index')->with('success', 'Investor beserta akun pengguna berhasil dihapus.');
    }
}
