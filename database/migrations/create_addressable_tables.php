<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Maggomann\LaravelAddressable\Models\Gender;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->environment('testing', 'local')) {
            $this->executeInDevelopmentProcess();

            return;
        }

        $this->executeInProductionProcess();
    }

    /**
     * Reverse the migrations.
     *
     * TODO: Delete the method before the PR goes into the master
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('addressable.tables.address_genders'));
        Schema::dropIfExists(config('addressable.tables.addresses'));
    }

    private function executeInDevelopmentProcess()
    {
        if (app()->environment('testing', 'local') === false) {
            return;
        }

        try {
            $this->executeMigrations();
        } catch (Exception $e) {
            $this->down();

            throw $e;
        }
    }

    private function executeInProductionProcess()
    {
        $this->executeMigrations();
    }

    private function executeMigrations()
    {
        Schema::create(config('addressable.tables.address_genders'), function (Blueprint $table) {
            $table->id();
            $table->string('title_translation_key');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('addressable.tables.addresses'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gender_id')->nullable()->index();
            $table->morphs('addressable');
            $table->string('first_name')->nullable()->index();
            $table->string('last_name')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('street')->nullable();
            $table->string('street_addition')->nullable();
            $table->string('street_address')->nullable();
            $table->string('label')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('state')->nullable();
            $table->string('company')->nullable()->index();
            $table->string('job_title')->nullable()->index();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_preferred')->default(false);
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        $this->addGenders();
    }

    private function addGenders(): void
    {
        $now = now();
        $genders = [
            [
                'title_translation_key' => 'address_genders.title.male',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title_translation_key' => 'address_genders.title.female',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title_translation_key' => 'address_genders.title.diverse',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Gender::insert($genders);
    }
};
