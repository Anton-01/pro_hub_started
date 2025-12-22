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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
            $table->uuid('company_id');

            // Datos personales
            $table->string('email', 255);
            $table->string('password', 255); // Hash con bcrypt
            $table->string('name', 255);
            $table->string('last_name', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('avatar_url', 500)->nullable();

            // Estado
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();

            // Tokens
            $table->string('remember_token', 100)->nullable();
            $table->string('password_reset_token', 255)->nullable();
            $table->timestamp('password_reset_expires_at')->nullable();
            $table->string('email_verification_token', 255)->nullable();

            // Metadatos
            $table->timestamps();
            $table->softDeletes();

            // Relación con empresa
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            // Índices
            $table->index('company_id');
            $table->index('email');
            $table->index('deleted_at');
        });

        // Agregar columnas con tipos PostgreSQL nativos
        DB::statement("ALTER TABLE users ADD COLUMN role user_role DEFAULT 'user'");
        DB::statement("ALTER TABLE users ADD COLUMN status record_status DEFAULT 'pending'");
        DB::statement("CREATE INDEX idx_users_role ON users(role)");
        DB::statement("CREATE INDEX idx_users_status ON users(status)");

        // Restricción única compuesta email + company_id
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_email_company_unique UNIQUE (email, company_id)");

        // Validación de formato de email
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_email_check CHECK (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
