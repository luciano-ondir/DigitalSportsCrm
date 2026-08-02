<?php

namespace App\Providers;

use App\Domain\EvtEvents\Actions\CreateIndividualAthleteEnrollmentAction;
use App\Domain\EvtEvents\Actions\GetAttributesAndRulesFromDisciplineAction;
use App\Domain\EvtEvents\Actions\ValidateAndSummarizeAthleteEnrollmentsAction;
use App\Models\RoutePermission;
use App\Plugins\PluginManager;
use App\Observers\DocumentObserver;
use App\Observers\RoutePermissionObserver;
use Domain\Documents\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Mail\MicrosoftGraphTransport;
use Illuminate\Support\Facades\Mail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registry of installed plugins (see App\Plugins\PluginServiceProvider).
        $this->app->singleton(PluginManager::class);

        $this->app->bind(CreateIndividualAthleteEnrollmentAction::class, function ($app) {
            return new CreateIndividualAthleteEnrollmentAction(
                $app->make(GetAttributesAndRulesFromDisciplineAction::class),
                $app->make(ValidateAndSummarizeAthleteEnrollmentsAction::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading();

        // Register model observers
        Document::observe(DocumentObserver::class);
        RoutePermission::observe(RoutePermissionObserver::class);

        Relation::morphMap([
            'entity' => \Domain\Entities\Models\Entity::class,
            'individual' => \Domain\Individuals\Models\Individual::class,
            'federation' => \Domain\Federations\Models\Federation::class,
        ]);

        Mail::extend('microsoft-graph', function (array $config = []) {
            return new MicrosoftGraphTransport(
                tenantId: config('services.microsoft_graph.tenant_id'),
                clientId: config('services.microsoft_graph.client_id'),
                clientSecret: config('services.microsoft_graph.client_secret'),
                fromAddress: config('services.microsoft_graph.from_address'),
            );
        });
    }
}
