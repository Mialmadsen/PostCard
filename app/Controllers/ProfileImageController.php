<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Repositories\UserRepository;
use PostCard\Services\CloudinaryService;

// Sends a private profile picture to the browser, e.g. /profile-image?user_id=3
class ProfileImageController
{
    public function show(): void
    {
        // This request only reads the session. Release its lock at once,
        // so the page's other photos can load at the same time.
        session_write_close();

        Auth::requireLogin();

        $userId = queryId('user_id');
        if ($userId === null) {
            abort(400, 'Ugyldigt bruger-ID.');
        }

        // You may see your own picture, or the picture of someone you share a group with
        $members = new GroupMemberRepository();
        $isMe = $userId === Auth::userId();
        if (!$isMe && !$members->sharesGroup(Auth::userId(), $userId)) {
            // 404, not 403: a stranger should not learn whether the user exists
            abort(404, 'Billedet blev ikke fundet.');
        }

        $users = new UserRepository();
        $image = $users->findProfileImage($userId);
        if ($image === null) {
            abort(404, 'Billedet blev ikke fundet.');
        }

        try {
            // The picture is private on Cloudinary, so the server fetches it
            $cloudinary = new CloudinaryService();
            $imageData = $cloudinary->downloadImage(
                $image['profile_image'],
                (int) $image['profile_image_version'],
                'c_limit,w_400,q_auto'
            );

            // Check that what came back really is a picture of that type
            $detectedMime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($imageData);
            if ($detectedMime !== 'image/webp') {
                throw new \RuntimeException('Cloudinary did not return the expected image.');
            }

            // Only this browser may keep it, for a day. A new picture has a new ?v= in the address,
            // so the browser never shows an old one.
            header('Cache-Control: private, max-age=86400');
            header_remove('Pragma');      // session_start() sent "don't cache" headers by default:
            header_remove('Expires');     // remove them, so they don't contradict the line above
            header('X-Content-Type-Options: nosniff');
            header('Content-Type: image/webp');
            header('Content-Length: ' . strlen($imageData));

            echo $imageData;
            exit;
        } catch (\RuntimeException $e) {
            // The technical reason goes to the log, not to the visitor
            error_log('Profile picture not loaded: ' . $e->getMessage());
            abort(502, 'Billedet kunne ikke indlæses.');
        }
    }
}
