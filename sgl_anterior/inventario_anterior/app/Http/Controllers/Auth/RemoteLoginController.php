<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\User;

class RemoteLoginController extends Controller
{
    /**
     * Logout helper.
     * 
     * @return [type] [description]
     */
    private function logout() {
        // Logout session
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Returns a remote token login credential.
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function generateRemoteToken(Request $request)
    {
        // ---- Parameter validation ------------------------------------------
        $rules = [
            'email' => 'required|string',
            'password' => 'required|string',
            'remote_email' => 'required|string',
            'remote_name' => 'required|string',
            'remote_user_id' => 'required|integer',
            'remote_system_id' => 'required|integer',
            'remote_profile_id' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Error en validación de parámetros: '.implode("\n",$validator->errors()->all()),
                'data' => null
            ]);
        }

        // ----- Login --------------------------------------------------------
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'can_transfer_login' => true])) {
            // Authentication passed...
            // Get/Add the remote user.
            $user = User::firstOrCreate(
                ['email' => $request->remote_email],
                [
                    'name' => $request->remote_name,
                    'password' => bcrypt(Str::random(32)),
                    'can_transfer_login' => false,
                ]
            );

            // Generate token & update
            $user->remote_user_id = $request->remote_user_id;
            $user->remote_system_id = $request->remote_system_id;
            $user->remote_profile_id = $request->remote_profile_id;
            $user->remote_token = Str::random(128);
            $user->remote_token_expiration = \Carbon\Carbon::now()->addMinute(1)->format('Y-m-d h:i:s');
            $user->save();

            // Logout session
            $this->logout();

            // Return data
            return response()->json([
                'status' => 'OK', 
                'message' => '', 
                'data' => [
                    'url' => route('remotelogin.remotelogin', [
                            'email' => $user->email, 
                            'token' => $user->remote_token
                        ]),
                    'expiration' => $user->remote_token_expiration
                ]
            ]);
        } else {
            // Authentication failed...
            return response()->json(['status' => 'ERROR', 'message' => 'Credenciales inválidas.', 'data' => null]);
        }
    }

    /**
     * Do a remote login using provided email & token.
     * 
     * @param  [type] $email [description]
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public function remoteLogin($email, $token) 
    {
        // ---- Parameter validation ------------------------------------------
        $rules = [
            'email' => 'required|email',
            'token' => 'required|string|regex:/^[a-z0-9]{128}$/i',
        ];

        $validator = Validator::make(['email' => $email, 'token' => $token], $rules);
        if ($validator->fails()) {
            $this->logout();
            abort(403, 'Unauthorized.');
        }

        // ----- Remote Login using token & expiration ------------------------
        $user = User::where('email', '=', $email)
            ->where('remote_token', '=', $token)
            ->where('remote_token_expiration', '>=', \Carbon\Carbon::now()->format('Y-m-d h:i:s'))
            ->first();

        if ($user) {
            // Authentication passed...
            Auth::login($user);
            return redirect()->route('activos.index');
        } else {
            // Authentication failed...
            $this->logout();
            abort(403, 'Unauthorized.');
        }
    }
}
