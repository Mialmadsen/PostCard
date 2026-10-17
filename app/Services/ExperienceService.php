<?php
declare(strict_types=1);

namespace PostCard\Services;

// Indeholder regler og beregninger for oplevelser. Kan evt genbruges i andre controllers, hvis vi har brug for at beregne status eller opslagstællere for oplevelser.
// Klassen henter ikke selv data fra databasen.
class ExperienceService
{
    // Beregner opslagstælleren ud fra gruppens indstillinger
    public function getPostCounts(array $experience, int $postsUsed): array
    {
        // Open-grupper har ingen opslagbegrænsning
        // Permission-grupper bruger oplevelsens opslaggrænse
        $postLimit = ($experience['posting_mode'] ?? '') === 'open'
            ? null
            : (int) $experience['post_limit'];

        // Beregner hvor mange opslag der er tilbage
        $postsRemaining = $postLimit !== null
            ? max(0, $postLimit - $postsUsed)
            : null;

        return [
            'postsUsed' => $postsUsed,
            'postLimit' => $postLimit,
            'postsRemaining' => $postsRemaining
        ];
    }

    // Beregner oplevelsens aktuelle status og antal dage tilbage
    public function getStatus(array $experience): array
    {
        $today = date('Y-m-d');
        $status = $experience['experience_status'];

        // Finder den danske statustekst
        if ($status === 'pending') {
            $statusText = 'Afventer godkendelse';
        } elseif ($status === 'rejected') {
            $statusText = 'Afvist';
        } elseif (
            $status === 'closed' ||
            $experience['end_date'] < $today
        ) {
            $statusText = 'Afsluttet';
        } elseif ($experience['start_date'] > $today) {
            $statusText = 'Kommende';
        } else {
            $statusText = 'I gang';
        }

        // En oplevelse er kun aktiv, når den er godkendt
        // og dags dato ligger inden for oplevelsens periode
        $isActive = (
            $status === 'approved' &&
            $experience['start_date'] <= $today &&
            $experience['end_date'] >= $today
        );

        // Beregner resterende dage, hvis oplevelsen er aktiv
        $daysRemaining = null;

        if ($isActive) {
            $todayDate = new \DateTimeImmutable($today);
            $endDate = new \DateTimeImmutable($experience['end_date']);

            $daysRemaining = (int) $todayDate->diff($endDate)->days;
        }

        return [
            'statusText' => $statusText,
            'isActive' => $isActive,
            'daysRemaining' => $daysRemaining
        ];
    }
}
