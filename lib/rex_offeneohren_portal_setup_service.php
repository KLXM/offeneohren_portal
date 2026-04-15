<?php

class rex_offeneohren_portal_setup_service
{
    /**
     * @return array<int, array{type:string,key:string,message:string,status:string}>
     */
    public static function syncAll(): array
    {
        $report = [];
        $report = array_merge($report, self::syncTemplates());
        $report = array_merge($report, self::syncModules());

        return $report;
    }

    /**
     * @return array<int, array{type:string,key:string,message:string,status:string}>
     */
    public static function syncTemplates(): array
    {
        $report = [];
        foreach (self::getTemplateSpecs() as $spec) {
            $report[] = self::upsertTemplate($spec);
        }

        return $report;
    }

    /**
     * @return array<int, array{type:string,key:string,message:string,status:string}>
     */
    public static function syncModules(): array
    {
        $report = [];
        foreach (self::getModuleSpecs() as $spec) {
            $report[] = self::upsertModule($spec['key'], $spec['name'], $spec['input'], $spec['output']);
        }

        rex_module_cache::deleteKeyMapping();

        return $report;
    }

    /**
     * @return array<int, array{key:string,name:string,file:string,active:int}>
     */
    private static function getTemplateSpecs(): array
    {
        return [
            [
                'key' => 'oo_portal_main',
                'name' => 'Offene Ohren Portal',
                'file' => rex_path::addon('offeneohren_portal', 'install/templates/oo_portal_main.php'),
                'active' => 1,
            ],
        ];
    }

    /**
     * @return array<int, array{key:string,name:string,input:string,output:string}>
     */
    private static function getModuleSpecs(): array
    {
        $base = rex_path::addon('offeneohren_portal', 'install/modules/');
        $items = [];

        foreach (scandir($base) ?: [] as $entry) {
            if ('.' === $entry || '..' === $entry) {
                continue;
            }

            $dir = $base . $entry . '/';
            if (!is_dir($dir)) {
                continue;
            }

            $metaFile = $dir . 'metadata.yml';
            $inputFile = $dir . 'input.php';
            $outputFile = $dir . 'output.php';

            if (!file_exists($metaFile) || !file_exists($inputFile) || !file_exists($outputFile)) {
                continue;
            }

            $meta = rex_string::yamlDecode((string) rex_file::get($metaFile));
            if (!is_array($meta) || empty($meta['key']) || empty($meta['name'])) {
                continue;
            }

            $items[] = [
                'key' => (string) $meta['key'],
                'name' => (string) $meta['name'],
                'input' => (string) rex_file::get($inputFile),
                'output' => (string) rex_file::get($outputFile),
            ];
        }

        return $items;
    }

    /**
     * @param array{key:string,name:string,file:string,active:int} $spec
     * @return array{type:string,key:string,message:string,status:string}
     */
    private static function upsertTemplate(array $spec): array
    {
        if (!file_exists($spec['file'])) {
            return self::line('template', $spec['key'], 'template source file not found', 'error');
        }

        $content = (string) rex_file::get($spec['file']);
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT id FROM ' . rex::getTable('template') . ' WHERE `key` = ?', [$spec['key']]);

        $tpl = rex_sql::factory();
        $tpl->setTable(rex::getTable('template'));
        $tpl->setValue('key', $spec['key']);
        $tpl->setValue('name', $spec['name']);
        $tpl->setValue('content', $content);
        $tpl->setValue('active', $spec['active']);
        $tpl->setArrayValue('attributes', [
            'ctype' => [],
            'modules' => [1 => ['all' => 1]],
            'categories' => ['all' => 1],
        ]);

        try {
            if ($sql->getRows() > 0) {
                $id = (int) $sql->getValue('id');
                $tpl->addGlobalUpdateFields();
                $tpl->setWhere(['id' => $id]);
                $tpl->update();
                rex_template_cache::delete($id);
                return self::line('template', $spec['key'], 'updated', 'success');
            }

            $tpl->addGlobalCreateFields();
            $tpl->insert();
            $id = (int) $tpl->getLastId();
            rex_template_cache::delete($id);
            return self::line('template', $spec['key'], 'created', 'success');
        } catch (rex_sql_exception $e) {
            return self::line('template', $spec['key'], $e->getMessage(), 'error');
        }
    }

    /**
     * @return array{type:string,key:string,message:string,status:string}
     */
    private static function upsertModule(string $key, string $name, string $input, string $output, string $type = 'module'): array
    {
        if ('' === trim($key)) {
            return self::line($type, '-', 'empty module key', 'error');
        }

        $existing = rex_sql::factory();
        $existing->setQuery('SELECT id FROM ' . rex::getTable('module') . ' WHERE `key` = ?', [$key]);

        try {
            if ($existing->getRows() > 0) {
                rex_sql::factory()->setQuery(
                    'UPDATE ' . rex::getTable('module') . ' SET `name` = :name, `input` = :input, `output` = :output WHERE `key` = :key',
                    [
                        ':name' => $name,
                        ':input' => $input,
                        ':output' => $output,
                        ':key' => $key,
                    ]
                );

                rex_module_cache::delete((int) $existing->getValue('id'));
                return self::line($type, $key, 'updated', 'success');
            }

            rex_sql::factory()->setQuery(
                'INSERT INTO ' . rex::getTable('module') . ' (`key`, `name`, `input`, `output`) VALUES (:key, :name, :input, :output)',
                [
                    ':key' => $key,
                    ':name' => $name,
                    ':input' => $input,
                    ':output' => $output,
                ]
            );

            return self::line($type, $key, 'created', 'success');
        } catch (rex_sql_exception $e) {
            return self::line($type, $key, $e->getMessage(), 'error');
        }
    }

    /**
     * @return array{type:string,key:string,message:string,status:string}
     */
    private static function line(string $type, string $key, string $message, string $status): array
    {
        return [
            'type' => $type,
            'key' => $key,
            'message' => $message,
            'status' => $status,
        ];
    }
}
