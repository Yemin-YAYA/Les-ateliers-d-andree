<?php

namespace App\Service;

class BreadcrumbService
{
    private array $breadcrumbs = [];

    public function addBreadcrumb(string $label, ?string $url = null): void
    {
        $this->breadcrumbs[] = ['label' => $label, 'url' => $url];
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}
