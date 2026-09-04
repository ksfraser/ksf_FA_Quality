<?php
declare(strict_types=1);

define('SS_ksf_FA_Quality', 147 << 8);

class hooks_ksf_FA_Quality extends hooks
{
    var $module_name = 'ksf_FA_Quality';
    var $version = '2.4.19-1.0.0';

    function install_extension($company, $force = false)
    {
        parent::install_extension($company, $force);

        $autoload = __DIR__ . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            return false;
        }
        require_once $autoload;

        $sqlFiles = [
            'sql/install.sql',
        ];

        foreach ($sqlFiles as $file) {
            $sqlFile = __DIR__ . '/' . $file;
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $prefix = get_company_preference($company)['_prefix'] ?? '0_';
                $sql = str_replace('0_', $prefix, $sql);
                run_db_import($sql, $company);
            }
        }

        return true;
    }

    function activate_extension($company, $force = false)
    {
        $this->install_extension($company, $force);
        add_security_section(SS_ksf_FA_Quality, 'Quality 8D', 'SA_QUALITY');
        return true;
    }

    function deactivate_extension($company, $force = false)
    {
        $prefix = get_company_preference($company)['_prefix'] ?? '0_';
        $tables = [
            "{$prefix}ksf_quality_8d",
            "{$prefix}ksf_quality_8d_team",
            "{$prefix}ksf_quality_8d_containment",
            "{$prefix}ksf_quality_8d_root_cause",
            "{$prefix}ksf_quality_8d_corrective_action",
            "{$prefix}ksf_quality_8d_implementation",
            "{$prefix}ksf_quality_8d_prevention",
            "{$prefix}ksf_quality_8d_recognition",
            "{$prefix}ksf_quality_8d_attachment",
        ];

        foreach ($tables as $table) {
            db_query("DROP TABLE IF EXISTS {$table}", "Cannot drop {$table}");
        }

        remove_security_section(SS_ksf_FA_Quality);
        return parent::deactivate_extension($company, $force);
    }

    function getModuleConstants(&$data, $opts = [])
    {
        $data['constants']['SS_ksf_FA_Quality'] = SS_ksf_FA_Quality;
        $data['constants']['SA_ksf_FA_QUALITY_8D'] = SS_ksf_FA_Quality | 1;
        $data['constants']['SA_ksf_FA_QUALITY_8D_VIEW'] = SS_ksf_FA_Quality | 2;
        return $data;
    }

    function getModuleCapabilities(&$data, $opts = [])
    {
        $data['capabilities']['quality_8d'] = [
            'create' => 'SA_ksf_FA_QUALITY_8D',
            'view' => 'SA_ksf_FA_QUALITY_8D_VIEW',
            'edit' => 'SA_ksf_FA_QUALITY_8D',
            'close' => 'SA_ksf_FA_QUALITY_8D',
        ];
        return $data;
    }

    function hook_invoke_all($hook, &$data)
    {
        $autoload = __DIR__ . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            return null;
        }
        require_once $autoload;

        return parent::hook_invoke_all($hook, $data);
    }
}