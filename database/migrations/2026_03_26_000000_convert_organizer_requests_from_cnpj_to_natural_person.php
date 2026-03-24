<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * For databases that already ran the older organization migration (cnpj columns).
     * New installs use the natural-person create migration and never have cnpj here.
     */
    public function up(): void
    {
        if (! Schema::hasTable('organizer_requests')) {
            return;
        }

        if (! Schema::hasColumn('organizer_requests', 'cnpj')) {
            return;
        }

        Schema::table('organizer_requests', function (Blueprint $table) {
            $table->dropUnique(['cnpj']);
        });

        Schema::table('organizer_requests', function (Blueprint $table) {
            $table->dropColumn([
                'cnpj',
                'phone_number',
                'website',
                'address',
                'organization_description',
            ]);
        });

        Schema::table('organizer_requests', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable()->unique();
            $table->text('reason')->nullable();
            $table->string('phone_number', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('organizer_requests')) {
            return;
        }

        if (Schema::hasColumn('organizer_requests', 'cnpj')) {
            return;
        }

        if (! Schema::hasColumn('organizer_requests', 'cpf')) {
            return;
        }

        Schema::table('organizer_requests', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'reason', 'phone_number']);
        });

        Schema::table('organizer_requests', function (Blueprint $table) {
            $table->string('cnpj', 18)->unique();
            $table->string('phone_number', 32);
            $table->string('website')->nullable();
            $table->text('address');
            $table->text('organization_description');
        });
    }
};
