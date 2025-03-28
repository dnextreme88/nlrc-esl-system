<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class ModuleCluster extends Cluster
{
    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Modules';
}
