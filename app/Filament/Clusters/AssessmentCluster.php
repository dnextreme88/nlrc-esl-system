<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class AssessmentCluster extends Cluster
{
    protected static ?string $activeNavigationIcon = 'heroicon-s-pencil-square';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Assessments';
}
