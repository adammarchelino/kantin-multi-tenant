<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use ReflectionClass;

/**
 * Provider dasar untuk seluruh modul aplikasi.
 *
 * Provider memuat route, view, dan namespace Livewire
 * berdasarkan lokasi provider milik setiap modul.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Alias yang digunakan oleh view dan Livewire.
     */
    abstract protected function moduleAlias(): string;

    public function boot(): void
    {
        $path = $this->modulePath();

        $this->loadViewsFrom(
            $path . '/resources/views',
            $this->moduleAlias()
        );

        Livewire::addNamespace(
            namespace: $this->moduleAlias(),
            viewPath: $path . '/resources/views/livewire',
        );

        foreach (glob($path . '/routes/*.php') ?: [] as $routeFile) {
            $this->loadRoutesFrom($routeFile);
        }
    }

    /**
     * Mengambil lokasi folder provider modul.
     */
    protected function modulePath(): string
    {
        return dirname(
            (string) (new ReflectionClass($this))->getFileName()
        );
    }
}
