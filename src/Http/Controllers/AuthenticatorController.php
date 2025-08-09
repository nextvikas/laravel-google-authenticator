<?php

namespace Nextvikas\Authenticator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nextvikas\Authenticator\Helpers\Authenticator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

class AuthenticatorController extends Controller
{

    protected Authenticator $authenticator;

    /**
     * Constructor to inject Authenticator helper and set context-specific config.
     *
     * @param Authenticator $authenticator
     */
    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }


    /**
     * Show the two-step verification page.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verify_two_step(Request $request)
    {
        $authenticator = $this->authenticator->getRoute($request);
        $role = collect($authenticator)->get(1, '');

        // Retrieve the guard name from the configuration.
        $guard_name = Config::get('authenticator.'.$role.'.login_guard_name');
        $secretColumn = Config::get('authenticator.otp_settings.secret_column_name');

        // Check if the user has an associated authenticator. If not, redirect to the scan page.
        if (empty(Auth::guard($guard_name)->user()->{$secretColumn})) {
            return redirect()->route('authenticator.'.$role.'.scan');
        }

        // Render the verification view.
        $config = Config::get('authenticator.'.$role);
        return view('authenticator::verify', compact('role', 'config'));
    }

    /**
     * Process the two-step verification form submission.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify_two_step_process(Request $request)
    {
        // Validate the incoming request for the verification code.
        $otp_digits = Config::get('authenticator.otp_settings.otp_digits');

        // Validate the incoming request for the verification code.
        $request->validate(['code' => 'required|numeric|digits:' . $otp_digits]);

        $authenticator = $this->authenticator->getRoute($request);
        $role = collect($authenticator)->get(1, '');

        // Retrieve the guard name from the configuration.
        $guard_name = Config::get('authenticator.'.$role.'.login_guard_name');

        $secretColumn = Config::get('authenticator.otp_settings.secret_column_name');
        // Attempt to decrypt the user's authenticator data.
        try {
            $decrypted = Crypt::decryptString(Auth::guard($guard_name)->user()->{$secretColumn});
        } catch (DecryptException $e) {
            $decrypted = ''; // If decryption fails, set to an empty string.
        }

        // Verify the code against the decrypted secret.
        $checkResult = $this->authenticator->verifyCode($decrypted, $request->get('code'));

        if (!$checkResult) {
            // If the verification fails, redirect back with an error message.
            return redirect()->back()->withErrors(['code' => ['Invalid Google Authenticator Code']]);
        } else {
            Session::put('TwoStepAuthenticator' . $role, true); // Use Session facade directly
            // On successful verification, store the session variable and redirect accordingly.
            $success_route_name = Config::get('authenticator.'.$role.'.success_route_name');
            return $success_route_name && Route::has($success_route_name) ? redirect()->route($success_route_name) : redirect()->intended('/');
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
        $otp_digits = Config::get('authenticator.otp_settings.otp_digits');

        // Validate the incoming request for the verification code.
        $request->validate(['code' => 'required|numeric|digits:' . $otp_digits]);

        $authenticator = $this->authenticator->getRoute($request);
        $role = collect($authenticator)->get(1, '');

        $sessionSecret = Session::get('auth_secret');

        if (empty($sessionSecret)) {
            // This should ideally not happen if flow is correct, but handles direct access or session timeout.
            return redirect()->route('authenticator.' . $role . '.scan')
                             ->withErrors(['code' => ['Session expired or secret not found. Please try scanning again.']]);
        }

        // Verify the code against the session's auth secret.
        $checkResult = $this->authenticator->verifyCode($sessionSecret, $request->get('code'));

        if (!$checkResult) {
            // If verification fails, redirect back with an error message.
            return redirect()->back()->withErrors(['code' => ['Invalid Google Authenticator Code']]);
        } else {
            // On successful verification, update the user's authenticator secret.
            $guard_name = Config::get('authenticator.'.$role.'.login_guard_name');
            $secretColumn = Config::get('authenticator.otp_settings.secret_column_name');


            $user = Auth::guard($guard_name)->user();
            if (!$user) {
                $login_route_name = Config::get($authenticator.'.login_route_name', 'login');
                if (Route::has($login_route_name)) {
                    return redirect()->route($login_route_name)->with('error', 'Please login first to setup 2FA.');
                } else {
                    return redirect('/')->with('error', 'Please login first to setup 2FA.');
                }
            }
            $user->{$secretColumn} = Crypt::encryptString($sessionSecret); // Dynamic column name
            $user->save();

            // Clear the secret from session as it's now saved in DB.
            Session::forget('auth_secret');

            // Store the session variable to indicate successful 2FA verification.
            Session::put('TwoStepAuthenticator' . $role, true);

            // Redirect to the success route or the intended URL/root.
            $successRouteName = Config::get('authenticator.'.$role.'.success_route_name');
            return $successRouteName && Route::has($successRouteName) ? redirect()->route($successRouteName) : redirect()->intended('/');
        }
    }


    private function replaceFormat($input, $data) {
        if(empty($input)) {
            return '';
        }
        if(empty($data)) {
            return $input;
        }
        // Use preg_replace_callback to find placeholders and replace them dynamically
        return preg_replace_callback('/\{(.*?)\}/', function($matches) use ($data) {
            // Get the placeholder name from $matches[1], and replace with corresponding data
            $placeholder = $matches[1] ?? '';
            return $data[$placeholder] ?? $matches[0] ?? ''; // Return original if not found
        }, $input);
    }


    /**
     * Show the QR code scanning page for two-step verification setup.
     *
     * @return \Illuminate\View\View
     */
    public function scan_two_step(Request $request)
    {
        $authenticator = $this->authenticator->getRoute($request);
        array_pop($authenticator);
        $authenticator = implode('.', $authenticator);

        // Generate and store a new auth secret if it doesn't already exist in the session.
        if (!Session::has('auth_secret')) {
            $secret = $this->authenticator->generateRandomSecret();
            Session::put('auth_secret', $secret);
        }

        // Retrieve the guard name from the configuration.
        $guard_name = Config::get($authenticator.'.login_guard_name', 'web');
        $app_name = Config::get('authenticator.otp_settings.app_format', Config::get('app.name', 'Laravel App'));

        $user = Auth::guard($guard_name)->user();

        if (!$user) {
            $login_route_name = Config::get($authenticator.'.login_route_name', 'login');
            if (Route::has($login_route_name)) {
                return redirect()->route($login_route_name)->with('error', 'Please login first to setup 2FA.');
            } else {
                return redirect('/')->with('error', 'Please login first to setup 2FA.');
            }
        }

        $userArray = $user->toArray();

        // Replace the format with dynamic data
        $output = $this->replaceFormat($app_name, $userArray);

        $qrCodeUrl = $this->authenticator->getQR($output, Session::get('auth_secret'));

        // Render the scan view with the QR code URL.
        return view('authenticator::scan', [
            'qrCodeUrl' => $qrCodeUrl,
            'fullrole' => $authenticator
        ]);
    }
}
