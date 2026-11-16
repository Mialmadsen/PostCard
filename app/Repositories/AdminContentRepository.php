<?php

declare(strict_types=1);

namespace PostCard\Repositories;

class AdminContentRepository extends Repository
{
    // De fire contentdele, som operatoren må redigere.
    public const ALLOWED_KEYS = [
        'welcome',
        'rules',
        'privacy',
        'contact'
    ];


    // Henter alle contentdele fra databasen.
    public function findAll(): array
    {
        $sql = "SELECT content_key, title, body, updated_at
                FROM site_content
                WHERE content_key IN (
                    'welcome',
                    'rules',
                    'privacy',
                    'contact'
                )";

        $rows = $this->fetchAll($sql);

        // Bruger content_key som nøgle, så vi nemt
        // kan finde fx $content['welcome'] i vores view.
        $content = [];

        foreach ($rows as $row) {
            $content[$row['content_key']] = $row;
        }

        return $content;
    }


    // Opdaterer et bestemt contentdel.
    public function update(
        string $contentKey,
        string $title,
        string $body,
        int $operatorId
    ): bool {

        // Kun de fire godkendte områder må ændres.
        if (!in_array($contentKey, self::ALLOWED_KEYS, true)) {
            return false;
        }

        // Kontrollerer at området findes i databasen.
        $sql = "SELECT COUNT(*)
                FROM site_content
                WHERE content_key = :content_key";

        $exists = $this->fetchValue($sql, [
            'content_key' => $contentKey
        ]);

        if ((int) $exists !== 1) {
            return false;
        }

        // Opdaterer den eksisterende række.
        $sql = "UPDATE site_content
                SET title = :title,
                    body = :body,
                    updated_by = :operator_id,
                    updated_at = NOW()
                WHERE content_key = :content_key";

        $this->execute($sql, [
            'title' => $title,
            'body' => $body,
            'operator_id' => $operatorId,
            'content_key' => $contentKey
        ]);

        return true;
    }
}
