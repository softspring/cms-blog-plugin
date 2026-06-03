<?php

return static function (array $data, int $originVersion, int $targetVersion): array {
    if ($originVersion < 2 && $targetVersion >= 2) {
        if (isset($data['title']) && is_string($data['title']) && '' !== $data['title']) {
            $data['title'] = [
                'en' => $data['title'],
                'es' => $data['title'],
            ];
        } else {
            $data['title'] = $data['title'] ?? [];
        }

        $data['limit'] = $data['limit'] ?? 5;
    }

    return $data;
};
