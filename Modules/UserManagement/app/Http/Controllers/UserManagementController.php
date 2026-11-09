<?php
 
namespace Modules\UserManagement\Http\Controllers;
 
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\UserManagement\Http\Requests\StoreUserRequest;
use Modules\UserManagement\Http\Requests\UpdateUserRequest;
 
class UserManagementController extends Controller
{
    /**
     * Display a listing of users, optionally filtered by keyword and role.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $role = UserRole::tryFrom($request->string('role')->toString());
 
        $users = User::query()
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn (Builder $query) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
 
        return view('usermanagement::index', [
            'users' => $users,
            'search' => $search,
            'selectedRole' => $role,
            'roles' => UserRole::cases(),
        ]);
    }
 
    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('usermanagement::create', ['roles' => UserRole::cases()]);
    }
 
    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());
 
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }
 
    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('usermanagement::edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }
 
    /**
     * Update the specified user. The password is only changed when filled in.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $attributes = $request->validated();
 
        if (blank($attributes['password'] ?? null)) {
            unset($attributes['password']);
        }
 
        $user->update($attributes);
 
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }
 
    /**
     * Remove the specified user. An admin cannot delete their own account.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
 
        $user->delete();
 
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}