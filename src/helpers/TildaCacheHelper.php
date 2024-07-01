<?php

namespace chemezov\luya\tilda\helpers;

class TildaCacheHelper
{
    public static function getProjectListCacheKey(): string
    {
        return 'tilda_project_list';
    }

    public static function getPageListCacheKey(int $projectId): array
    {
        return ['tilda_page_list', 'project_id' => $projectId];
    }

    public static function getPageExportCacheKey(int $pageId): array
    {
        return ['tilda_page_export', 'page_id' => $pageId];
    }

    public static function getProjectExportCacheKey(int $projectId): array
    {
        return ['tilda_project_export', 'project_id' => $projectId];
    }

    public static function getProjectAssetCacheKey(int $projectId): array
    {
        return ['tilda_project_asset', 'project_id' => $projectId];
    }

    public static function getPageAssetCacheKey(int $pageId): array
    {
        return ['tilda_page_asset', 'page_id' => $pageId];
    }
}
