<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Habilitar extensión UUID para PostgreSQL
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto"');

        // Crear tipos enumerados
        DB::statement("DO $$ BEGIN
            CREATE TYPE user_role AS ENUM ('super_admin', 'admin', 'user');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;");

        DB::statement("DO $$ BEGIN
            CREATE TYPE record_status AS ENUM ('active', 'inactive', 'pending', 'suspended');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;");

        DB::statement("DO $$ BEGIN
            CREATE TYPE module_type AS ENUM ('link', 'modal', 'external');
        EXCEPTION
            WHEN duplicate_object THEN null;
        END $$;");

        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->string('name', 255);
            $table->string('slug', 255)->unique();

            $table->string('domain')->nullable()->unique(); // Para multi-tenant
            $table->string('contact_email')->nullable();
            $table->json('settings')->nullable();

            $table->string('tax_id', 50)->nullable(); // RFC en México
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('website', 255)->nullable();
            $table->integer('max_admins')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('deleted_at');
        });

        // Agregar columna status usando tipo nativo de PostgreSQL
        DB::statement("ALTER TABLE companies ADD COLUMN status record_status DEFAULT 'active'");
        DB::statement("CREATE INDEX idx_companies_status ON companies(status)");

        // Agregar constraint de validación
        DB::statement("ALTER TABLE companies ADD CONSTRAINT companies_name_check CHECK (char_length(name) >= 2)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
        DB::statement('DROP TYPE IF EXISTS module_type');
        DB::statement('DROP TYPE IF EXISTS record_status');
        DB::statement('DROP TYPE IF EXISTS user_role');
    }
};
