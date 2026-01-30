<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request)
    {
        // Almacena la URL actual en la sesión
        $request->session()->put('url.intended', $request->redirect);

        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        // Si hay una URL intended, usarla (para casos donde el usuario viene de una página específica)
        if ($request->session()->has('url.intended')) {
            $intendedUrl = $request->session()->get('url.intended');
            // Si la URL intended es la ruta de post-login de INFRASTOCK, permitirla
            if (strpos($intendedUrl, 'infrastock/post-login') !== false) {
                return redirect()->intended();
            }
        }
        
        // Verificar si el usuario tiene roles de INFRASTOCK y redirigir a la página principal
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Verificar roles de INFRASTOCK (app_id = 19)
        $infrastockRoles = ['Aseo', 'Personal de Aseo', 'Psicola', 'Ciencias Basicas', 'Operario', 
                           'Centro de Convivencia', 'Ganaderia', 'Vigilancia', 'Agroindustria', 
                           'Instructor', 'Administrador'];
        
        $hasInfrastockRole = false;
        foreach ($userRoles as $roleName) {
            if (in_array($roleName, $infrastockRoles)) {
                $hasInfrastockRole = true;
                break;
            }
        }
        
        // Si tiene rol de INFRASTOCK, redirigir a la página principal de SICEFA
        if ($hasInfrastockRole) {
            return redirect()->route('cefa.welcome');
        }
        
        // Para otros usuarios, usar la redirección estándar
        return redirect()->intended($this->redirectPath());
    }
}
