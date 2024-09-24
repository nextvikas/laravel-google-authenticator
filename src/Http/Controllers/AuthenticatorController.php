<?php

namespace Nextvikas\Authenticator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nextvikas\Authenticator\Helpers\Authenticator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class AuthenticatorController extends Controller
{
    /**
     * Show the two-step verification page.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verify_two_step(Request $request)
    {
        $role = $request->attributes->get('customRole') ?? '';

        // Retrieve the guard name from the configuration.
        $guard_name = Config::get('authenticator.'.$role.'.login_guard_name');

        // Check if the user has an associated authenticator. If not, redirect to the scan page.
        if (empty(Auth::guard($guard_name)->user()->authenticator)) {
            return redirect()->route('authenticator.'.$role.'.scan');
        }

        // Render the verification view.
        return view('authenticator::verify', compact('role'));
    }

    /**
     * Process the two-step verification form submission.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify_two_step_process(Request $request)
    {
        $role = $request->attributes->get('customRole') ?? '';

        // Validate the incoming request for the verification code.
        $this->validate($request, ['code' => 'required|numeric|min:6']);

        // Retrieve the guard name from the configuration.
        $guard_name = Config::get('authenticator.'.$role.'.login_guard_name');

        // Attempt to decrypt the user's authenticator data.
        try {
            $decrypted = Crypt::decryptString(Auth::guard($guard_name)->user()->authenticator);
        } catch (DecryptException $e) {
            $decrypted = ''; // If decryption fails, set to an empty string.
        }

        // Verify the code against the decrypted secret.
        $checkResult = (new Authenticator)->verifyCode($decrypted, $request->get('code'), 2);

        if (!$checkResult) {
            // If the verification fails, redirect back with an error message.
            return redirect()->back()->withErrors(['code' => ['Invalid Google Authenticator Code']]);
        } else {
            // On successful verification, store the session variable and redirect accordingly.
            $success_route_name = Config::get('authenticator.'.$role.'.success_route_name');
            $request->session()->put('TwoStepAuthenticator'.$role, true);
            return $success_route_name ? redirect()->route($success_route_name) : redirect()->to('/');
        }
    }

    /**
     * Process the two-step scan verification.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function scan_two_step_process(Request $request)
    {
        // Validate the incoming request for the verification code.
        $this->validate($request, ['code' => 'required|numeric|min:6']);

        $role = $request->attributes->get('customRole') ?? '';

        // Verify the code against the session's auth secret.
        $checkResult = (new Authenticator)->verifyCode($request->session()->get('auth_secret'), $request->get('code'), 2);

        if (!$checkResult) {
            // If verification fails, redirect back with an error message.
            return redirect()->back()->withErrors(['code' => ['Invalid Google Authenticator Code']]);
        } else {
            // On successful verification, update the user's authenticator secret.
            $guard_name = Config::get('authenticator.'.$role.'.login_guard_name');
            $userModel = Config::get('auth.providers.users.model');

            $userId = Auth::guard($guard_name)->user()->id;
            $user = $userModel::findOrFail($userId);
            $user->authenticator = Crypt::encryptString($request->session()->get('auth_secret'));
            $user->save();

            // Store the session variable to indicate successful verification.
            $request->session()->put('TwoStepAuthenticator'.$role, true);

            // Redirect to the success route or the root.
            $success_route_name = Config::get('authenticator.'.$role.'.success_route_name');
            return $success_route_name ? redirect()->route($success_route_name) : redirect()->to('/');
        }
    }


    private function replaceFormat($input, $data) {
        // Use preg_replace_callback to find placeholders and replace them dynamically
        return preg_replace_callback('/\{(.*?)\}/', function($matches) use ($data) {
            // Get the placeholder name from $matches[1], and replace with corresponding data
            $placeholder = $matches[1];
            return isset($data[$placeholder]) ? $data[$placeholder] : $matches[0]; // Return original if not found
        }, $input);
    }


    /**
     * Show the QR code scanning page for two-step verification setup.
     *
     * @return \Illuminate\View\View
     */
    public function scan_two_step(Request $request)
    {
        $role = $request->attributes->get('customRole') ?? '';

        // Generate and store a new auth secret if it doesn't already exist in the session.
        if (!request()->session()->has('auth_secret')) {
            $secret = (new Authenticator)->generateRandomSecret();
            request()->session()->put('auth_secret', $secret);
        }

        // Retrieve the guard name from the configuration.
        $guard_name = Config::get('authenticator.'.$role.'.login_guard_name');
        $app_name = Config::get('authenticator.app_format');

        $userArray =  Auth::guard($guard_name)->user()->toArray();

        // Replace the format with dynamic data
        $output = $this->replaceFormat($app_name, $userArray);

        $qrCodeUrl = (new Authenticator)->getQR($output, request()->session()->get('auth_secret'));

        // Render the scan view with the QR code URL.
        return view('authenticator::scan', [
            'qrCodeUrl' => $qrCodeUrl,
            'role' => $role
        ]);
    }
}
