<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Category;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\CreateLevelRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Level;
use App\Team;
use App\User;
use Auth;
use App\Http\Requests;
use Illuminate\Http\Request;

class AdminController extends AdminBaseController
{

    public function login()
    {
        $admins = Admin::all();

        return view('admin.login', ['pages' => [], 'admins' => $admins]);
    }

    public function authenticate(AdminLoginRequest $request)
    {
        $adminId = $request->admin_id;
        $password = $request->password;

        if (Auth::guard('admin')->attempt(['id' => $adminId, 'password' => $password])) {
            return redirect()->intended('admin.dashboard');
        }

        return redirect()->back()->withErrors(['password' => 'Falsches Passwort']);
    }

    public function dashboard()
    {
        return $this->adminView('dashboard');
    }

    public function users()
    {
        $users = User::all();

        return $this->adminView('users', ['users' => $users]);
    }

    public function user($id = null)
    {
        $user = new User();
        $new = true;

        if ($id !== null) {
            $user = User::findOrFail($id);
            $new = false;
        }

        return $this->adminView('user', ['user' => $user, 'new' => $new]);
    }

    public function saveUser(UserRegisterRequest $request)
    {
        $user = User::firstOrNew(['id' => $request->get('id')]);

        $user->fill($request->all());
        $user->save();

        return redirect('/admin/users');
    }

    public function levels()
    {
        $categories = Category::all();
        $levels = Level::all()->sortBy('order');

        return $this->adminView('levels', ['categories' => $categories, 'levels' => $levels]);
    }

    public function level($id = null)
    {
        $level = new Level();
        $new = true;
        $categories = Category::all();

        if($id !== null) {
            $level = Level::findOrFail($id);
            $new = false;
        }

        return $this->adminView('level', ['level' => $level, 'new' => $new, 'categories' => $categories]);
    }

    public function saveLevel(CreateLevelRequest $request)
    {
        $level = Level::firstOrNew(['id' => $request->get('id')]);

        $level->fill($request->all());
        $level->save();

        return redirect('/admin/levels');
    }

    public function deleteLevel($id)
    {
        Level::destroy($id);

        return response()->json(true);
    }

    public function teams()
    {
        return $this->adminView('teams', ['teams' => Team::all()]);
    }

    public function createTeam() {
        return $this->adminView('team.create');
    }

    public function storeTeam(Request $request) {
        $team = new Team();
        $team->name = $request->name;
        $team->save();

        return $this->adminView('teams', ['teams' => Team::all()]);
    }

    public function editTeam($id) {
        return $this->adminView('team.edit', ['team' => Team::findOrFail($id)]);
    }

    public function updateTeam(Request $request, $id) {
        $team = Team::findOrFail($id);
        $team->name = $request->name;
        $team->save();

        return $this->adminView('teams', ['teams' => Team::all()]);
    }

    public function statistics()
    {
        return $this->adminView('statistics');
    }

}
