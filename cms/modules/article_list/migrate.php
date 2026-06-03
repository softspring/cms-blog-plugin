<?php

return static function (array $data, int $originVersion, int $targetVersion): array {
    if ($originVersion < 2 && $targetVersion >= 2) {
        $data['show_filter_form'] = $data['show_filter_form'] ?? true;
        $data['tag'] = $data['tag'] ?? '';
        $data['limit'] = $data['limit'] ?? '';
        $data['order'] = $data['order'] ?? 'published_at_desc';
    }

    return $data;
};
