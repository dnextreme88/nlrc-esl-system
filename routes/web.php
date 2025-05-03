<?php

use App\Livewire\Announcements\AnnouncementDetail;
use App\Livewire\Announcements\AnnouncementList;
use App\Livewire\Assessments\AssessmentDetail;
use App\Livewire\Assessments\AssessmentList;
use App\Livewire\Assessments\AssessmentResult;
use App\Livewire\Homepage;
use App\Livewire\Meetings\MeetingDetail;
use App\Livewire\Meetings\TeacherAvailabilitySlots;
use App\Livewire\Modules\ModuleDetail;
use App\Livewire\Modules\ModuleList;
use App\Livewire\Modules\Units\UnitDetail;
use App\Livewire\MyMeetings;
use App\Livewire\NotificationList;
use App\Livewire\Settings\SecuritySettings;
use App\Livewire\Settings\SettingsPage;
use App\Livewire\Settings\TimeSettings;
use App\Livewire\Settings\UserSettings;
use Illuminate\Support\Facades\Route;

Route::get('/', Homepage::class)->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/my-meetings', MyMeetings::class)->name('my-meetings');
    Route::get('/notifications', NotificationList::class)->name('notifications');

    Route::group(['prefix' => 'announcements', 'as' => 'announcements.'], function () {
        Route::get('/', AnnouncementList::class)->name('index');
        Route::get('/{id}-{slug}', AnnouncementDetail::class)->name('detail');
    });

    Route::group(['prefix' => 'assessments', 'as' => 'assessments.'], function () {
        Route::get('/', AssessmentList::class)->name('index');
        Route::get('/answers/{assessment_uuid}', AssessmentResult::class)->name('result');
        Route::get('/{id}-{slug}', AssessmentDetail::class)->name('detail');
    });

    Route::group(['prefix' => 'meetings', 'as' => 'meetings.'], function () {
        Route::get('/details/{meeting_uuid}', MeetingDetail::class)->name('detail');
        Route::get('/availability', TeacherAvailabilitySlots::class)->name('availability');
    });

    Route::group(['prefix' => 'modules', 'as' => 'modules.'], function () {
        Route::get('/', ModuleList::class)->name('index');
        Route::get('/{id}-{slug}', ModuleDetail::class)->name('detail');
        Route::get('/{id}-{slug}/units/{unit_id}-{unit_slug}', UnitDetail::class)->name('unit_detail');
    });

    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', SettingsPage::class)->name('index');
        Route::get('/security', SecuritySettings::class)->name('security');
        Route::get('/time', TimeSettings::class)->name('time');
        Route::get('/user', UserSettings::class)->name('user');
    });
});
