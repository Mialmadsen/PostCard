<?php

declare(strict_types=1);

namespace PostCard\Repositories;

class AdminSettingsRepository extends Repository
{
    // De indstillinger, operatoren må ændre.
    public const ALLOWED_KEYS = [
        'default_post_limit',
        'max_upload_size',
        'allow_likes',
        'allow_comments',
    ];


    // Henter alle indstillinger fra databasen.
    public function findAll(): array
    {
        $rows = $this->fetchAll(
            "SELECT setting_key, setting_value
             FROM site_settings
             WHERE setting_key IN (
                 'default_post_limit',
                 'max_upload_size',
                 'allow_likes',
                 'allow_comments'
             )"
        );

        $settings = [];

        // Gør indstillingerne nemme at finde via deres nøgle.
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }


    // Opdaterer en eksisterende indstilling.
    public function update(string $settingKey, string $value): bool
    {
        // Tillader kun de fire indstillinger.
        if (!in_array($settingKey, self::ALLOWED_KEYS, true)) {
            return false;
        }

        // Kontrollerer at indstillingen findes i databasen.
        $exists = $this->fetchValue(
            "SELECT COUNT(*)
             FROM site_settings
             WHERE setting_key = :setting_key",
            ['setting_key' => $settingKey]
        );

        if ((int) $exists !== 1) {
            return false;
        }

        // Gemmer den nye værdi.
        $this->execute(
            "UPDATE site_settings
             SET setting_value = :setting_value
             WHERE setting_key = :setting_key",
            [
                'setting_value' => $value,
                'setting_key' => $settingKey,
            ]
        );

        return true;
    }
}
