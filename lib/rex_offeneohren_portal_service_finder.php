<?php

class rex_offeneohren_portal_service_finder
{
    /**
     * @return array{q:string,district_id:int,group_id:int,language_id:int}
     */
    public static function getRequestFilters(): array
    {
        return [
            'q' => trim(rex_request('q', 'string', '')),
            'district_id' => rex_request('district_id', 'int', 0),
            'group_id' => rex_request('group_id', 'int', 0),
            'language_id' => rex_request('language_id', 'int', 0),
        ];
    }

    /**
     * @return rex_offeneohren_portal_service[]
     */
    public static function find(array $filters, int $limit = 100): array
    {
        $query = rex_offeneohren_portal_service::query();
        $query->where('status', 1);

        if (!empty($filters['district_id'])) {
            $query->whereRaw('FIND_IN_SET(:district_id, district_id)', ['district_id' => (int) $filters['district_id']]);
        }

        $query->orderBy('name', 'ASC');
        $services = $query->find()->toArray();

        $out = [];
        $needle = mb_strtolower((string) ($filters['q'] ?? ''));
        $groupId = (int) ($filters['group_id'] ?? 0);
        $languageId = (int) ($filters['language_id'] ?? 0);

        foreach ($services as $service) {
            if ($needle !== '') {
                $haystack = mb_strtolower(implode(' ', [
                    (string) $service->getValue('name'),
                    (string) $service->getValue('city'),
                    (string) $service->getValue('description'),
                ]));

                if (mb_stripos($haystack, $needle) === false) {
                    continue;
                }
            }

            if ($groupId > 0) {
                $groupIds = array_map('intval', $service->getRelatedCollection('group_ids')->getIds());
                if (!in_array($groupId, $groupIds, true)) {
                    continue;
                }
            }

            if ($languageId > 0) {
                $languageIds = array_map('intval', $service->getRelatedCollection('language_ids')->getIds());
                if (!in_array($languageId, $languageIds, true)) {
                    continue;
                }
            }

            $out[] = $service;
            if (count($out) >= $limit) {
                break;
            }
        }

        return $out;
    }

    private static function getAvailableOptionIds(array $filters, string $type): array
    {
        $dummyFilters = $filters;
        if ($type === 'district') $dummyFilters['district_id'] = 0;
        if ($type === 'group') $dummyFilters['group_id'] = 0;
        if ($type === 'language') $dummyFilters['language_id'] = 0;

        $services = self::find($dummyFilters, 9999);
        $ids = [];

        foreach ($services as $service) {
            if ($type === 'district') {
                if ($val = (string) $service->getValue('district_id')) {
                    $parts = array_filter(array_map('intval', explode(',', $val)));
                    foreach ($parts as $pId) {
                        $ids[] = $pId;
                    }
                }
            } elseif ($type === 'group') {
                foreach ($service->getRelatedCollection('group_ids')->getIds() as $gId) {
                    $ids[] = (int) $gId;
                }
            } elseif ($type === 'language') {
                foreach ($service->getRelatedCollection('language_ids')->getIds() as $lId) {
                    $ids[] = (int) $lId;
                }
            }
        }
        return array_unique($ids);
    }

    /**
     * @return array<int, string>
     */
    public static function districtOptions(array $filters = []): array
    {
        $out = [0 => 'Bitte wählen...'];
        $available = empty($filters) ? null : self::getAvailableOptionIds($filters, 'district');
        
        $topDistricts = [];
        $otherDistricts = [];

        $districtsQuery = rex_offeneohren_portal_district::query()->orderBy('name', 'ASC');
        foreach ($districtsQuery->find() as $district) {
            $id = (int) $district->getId();
            if ($available === null || in_array($id, $available, true)) {
                if ($id === 31 || $id === 32) {
                    $topDistricts[$id] = (string) $district->getValue('name');
                } else {
                    $otherDistricts[$id] = (string) $district->getValue('name');
                }
            }
        }

        // Bundesweit (32) first, then Hessenweit (31)
        if (isset($topDistricts[32])) {
            $out[32] = $topDistricts[32];
        }
        if (isset($topDistricts[31])) {
            $out[31] = $topDistricts[31];
        }

        if (!empty($topDistricts) && !empty($otherDistricts)) {
            $out[-1] = '──────────';
        }

        foreach ($otherDistricts as $id => $label) {
            $out[$id] = $label;
        }

        return $out;
    }

    /**
     * @return array<int, string>
     */
    public static function groupOptions(array $filters = []): array
    {
        $out = [0 => 'Bitte wählen...'];
        $available = empty($filters) ? null : self::getAvailableOptionIds($filters, 'group');
        
        $groupsQuery = rex_offeneohren_portal_group::query()->orderBy('name', 'ASC');
        foreach ($groupsQuery->find() as $group) {
            if ($available === null || in_array((int)$group->getId(), $available, true)) {
                $out[(int) $group->getId()] = (string) $group->getValue('name');
            }
        }

        return $out;
    }

    /**
     * @return array<int, string>
     */
    public static function languageOptions(array $filters = []): array
    {
        $out = [0 => 'Bitte wählen...'];
        $available = empty($filters) ? null : self::getAvailableOptionIds($filters, 'language');
        
        $languagesQuery = rex_offeneohren_portal_language::query()->orderBy('name', 'ASC');
        foreach ($languagesQuery->find() as $language) {
            if ($available === null || in_array((int)$language->getId(), $available, true)) {
                $out[(int) $language->getId()] = (string) $language->getValue('name');
            }
        }

        return $out;
    }
}
