<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\UserRepository;
use PostCard\Services\UserValidator;
use PostCard\Core\Session;
use PostCard\Services\CloudinaryService;

// Shows information on settings page
class SettingsController
{
    public function show(): void
    {
        Auth::requireLogin();

        $users = new UserRepository();
        $user = $users->findForSettings(Auth::userId());

        // The "saved" message from the last save, shown once (F5 removes it)
        $settingsMessage = Session::takeFlash('settings_message');

        View::render('settings', [
            'pageTitle' => 'Indstillinger',
            'isLoggedIn' => true,
            'pageScripts' => ['avatar-preview', 'confirm', 'password-toggle'],
            'user' => $user,
            'old' => $user,
            'errors' => [],
            'pictureError' => null,
            'settingsMessage' => $settingsMessage,
            'passwordErrors' => []

        ]);
    }

    // Changing details in profile
    public function updateDetails(): void
    {
        Auth::requireLogin();
        $userId = Auth::userId();

        $firstName = input('first_name');
        $lastName = input('last_name');
        $username = input('username');
        $email = mb_strtolower(input('email'));
        $birthdate = input('birthdate');

        $validator = new UserValidator();
        $errors = $validator->validateDetails($firstName, $lastName, $username, $email, $birthdate, $userId);

        $users = new UserRepository();
        // the saved row: for the avatar, and to see whether the e-mail changed
        $user = $users->findForSettings($userId);

        if ($errors !== []) {
            http_response_code(422);
            View::render('settings', [
                'pageTitle' => 'Indstillinger',
                'isLoggedIn' => true,
                'pageScripts' => ['avatar-preview', 'confirm', 'password-toggle'],
                'user' => $user,
                'old' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'birthdate' => $birthdate,

                ],
                'errors' => $errors,
                'pictureError' => null,
                'settingsMessage' => null,
                'passwordErrors' => []
            ]);
            return;
        }

        $users->updateDetails($userId, $firstName, $lastName, $username, $email, $birthdate);

        if ($email !== $user['email']) {
            $users->clearEmailVerified($userId);
        }

        Session::flash('settings_message', 'Dine oplysninger er gemt.');
        redirect('/settings');
    }

    // Saving a new profile picture: upload the new one, save it, then delete the old one
    public function updatePicture(): void
    {
        Auth::requireLogin();
        $userId = Auth::userId();

        $users = new UserRepository();
        // the saved row: the old picture's public_id, and the fields if the page is shown again
        $user = $users->findForSettings($userId);

        $cloudinary = new CloudinaryService();

        // 1. Upload first: if it fails, the old picture is still there
        try {
            $image = $cloudinary->upload($_FILES['profile_image'] ?? []);
        } catch (\RuntimeException $e) {
            http_response_code(422);
            View::render('settings', [
                'pageTitle' => 'Indstillinger',
                'isLoggedIn' => true,
                'pageScripts' => ['avatar-preview', 'confirm', 'password-toggle'],
                'user' => $user,
                'old' => $user,
                'errors' => [],
                'pictureError' => $e->getMessage(),
                'settingsMessage' => null,
                'passwordErrors' => []
            ]);
            return;
        }

        // 2. Save the new picture's id, version and format
        $users->updateProfileImage($userId, $image['public_id'], $image['version'], $image['format']);

        // 3. The old picture is not used any more, so it is deleted on Cloudinary
        if ($user['profile_image'] !== null) {
            try {
                $cloudinary->delete($user['profile_image']);
            } catch (\RuntimeException $e) {
                // The new picture is already saved, so a leftover old file is only logged
                error_log('Old profile picture not deleted: ' . $e->getMessage());
            }
        }

        Session::flash('settings_message', 'Dit profilbillede er gemt.');
        redirect('/settings');
    }

    // Removing the profile picture: the initials are shown again
    public function removePicture(): void
    {
        Auth::requireLogin();
        $userId = Auth::userId();

        $users = new UserRepository();
        $user = $users->findForSettings($userId);

        if ($user['profile_image'] !== null) {
            // 1. Empty the three columns first: from now on nobody sees the picture
            $users->updateProfileImage($userId, null, null, null);

            // 2. Then delete the file; if that fails, the picture is already gone for everyone
            try {
                $cloudinary = new CloudinaryService();
                $cloudinary->delete($user['profile_image']);
            } catch (\RuntimeException $e) {
                error_log('Removed profile picture not deleted: ' . $e->getMessage());
            }
        }

        Session::flash('settings_message', 'Dit profilbillede er fjernet.');
        redirect('/settings');
    }

    //We change password in profile 
    public function updatePassword(): void
    {
        Auth::requireLogin();
        $userId = Auth::userId();

        // false = do not trim: a space is a real part of a password
        $currentPassword = input('current_password', false);
        $newPassword = input('password', false);
        $newPasswordConfirm = input('password_confirm', false);

        // The rules for the new password, the same as on register: keys 'password' and 'password_confirm'
        $validator = new UserValidator();
        $passwordErrors = $validator->validatePassword($newPassword, $newPasswordConfirm);

        // The current password proves it is really you
        $users = new UserRepository();
        $hash = $users->findPasswordHash($userId) ?? '';
        if (!password_verify($currentPassword, $hash)) {
            $passwordErrors['current_password'] = 'Den nuværende adgangskode er forkert.';
        }

        if ($passwordErrors !== []) {
            $user = $users->findForSettings($userId);
            http_response_code(422);
            View::render('settings', [
                'pageTitle' => 'Indstillinger',
                'isLoggedIn' => true,
                'passwordErrors' => $passwordErrors,
                'pageScripts' => ['avatar-preview', 'confirm', 'password-toggle'],
                'user' => $user,
                'old' => $user,
                'errors' => [],
                'pictureError' => null,
                'settingsMessage' => null,

            ]);
            return;
        }

        $users->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));

        //We need a new session
        Session::regenerate();

        Session::flash('settings_message', 'Din adgangskode er ændret.');
        redirect('/settings');
    }
}
