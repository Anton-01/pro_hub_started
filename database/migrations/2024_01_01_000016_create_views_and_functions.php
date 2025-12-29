<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Función para actualizar updated_at automáticamente
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS \$\$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            \$\$ language 'plpgsql';
        ");

        // Triggers para updated_at
        $tables = [
            'companies',
            'company_configurations',
            'users',
            'modules',
            'calendar_events',
            'news',
            'contacts',
            'banner_images',
            'cache_settings'
        ];

        foreach ($tables as $table) {
            DB::unprepared("
                DROP TRIGGER IF EXISTS update_{$table}_updated_at ON {$table};
                CREATE TRIGGER update_{$table}_updated_at
                BEFORE UPDATE ON {$table}
                FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
            ");
        }

        // Función: Verificar límite de admins por empresa
        DB::unprepared("
            CREATE OR REPLACE FUNCTION check_admin_limit()
            RETURNS TRIGGER AS \$\$
            DECLARE
                admin_count INTEGER;
                max_admins_limit INTEGER;
            BEGIN
                IF NEW.role IN ('super_admin', 'admin') AND NEW.deleted_at IS NULL THEN
                    SELECT COUNT(*) INTO admin_count
                    FROM users
                    WHERE company_id = NEW.company_id
                      AND role IN ('super_admin', 'admin')
                      AND deleted_at IS NULL
                      AND id != COALESCE(NEW.id, uuid_nil());

                    SELECT c.max_admins INTO max_admins_limit
                    FROM companies c
                    WHERE c.id = NEW.company_id;

                    IF admin_count >= max_admins_limit THEN
                        RAISE EXCEPTION 'Se ha alcanzado el límite máximo de administradores (%) para esta empresa', max_admins_limit;
                    END IF;
                END IF;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS check_admin_limit_trigger ON users;
            CREATE TRIGGER check_admin_limit_trigger
            BEFORE INSERT OR UPDATE ON users
            FOR EACH ROW EXECUTE FUNCTION check_admin_limit();
        ");

        // Función: Verificar que existe un super_admin por empresa
        DB::unprepared("
            CREATE OR REPLACE FUNCTION ensure_super_admin()
            RETURNS TRIGGER AS \$\$
            DECLARE
                super_admin_count INTEGER;
            BEGIN
                IF OLD.role = 'super_admin' AND (NEW.role != 'super_admin' OR NEW.deleted_at IS NOT NULL) THEN
                    SELECT COUNT(*) INTO super_admin_count
                    FROM users
                    WHERE company_id = OLD.company_id
                      AND role = 'super_admin'
                      AND deleted_at IS NULL
                      AND id != OLD.id;

                    IF super_admin_count = 0 THEN
                        RAISE EXCEPTION 'No se puede eliminar o cambiar el rol del único super administrador de la empresa';
                    END IF;
                END IF;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS ensure_super_admin_trigger ON users;
            CREATE TRIGGER ensure_super_admin_trigger
            BEFORE UPDATE OR DELETE ON users
            FOR EACH ROW EXECUTE FUNCTION ensure_super_admin();
        ");

        // Vista: Usuarios activos con información de empresa
        DB::unprepared("
            CREATE OR REPLACE VIEW v_active_users AS
            SELECT
                u.id,
                u.email,
                u.name,
                u.last_name,
                u.role,
                u.status,
                u.last_login_at,
                c.id as company_id,
                c.name as company_name,
                c.slug as company_slug
            FROM users u
            INNER JOIN companies c ON u.company_id = c.id
            WHERE u.deleted_at IS NULL
              AND u.status = 'active'
              AND c.deleted_at IS NULL;
        ");

        // Vista: Noticias activas (para el cintillo)
        DB::unprepared("
            CREATE OR REPLACE VIEW v_active_news AS
            SELECT
                n.id,
                n.company_id,
                n.title,
                n.content,
                n.url,
                n.priority,
                n.is_active
            FROM news n
            WHERE n.deleted_at IS NULL
              AND n.is_active = true
              AND (n.published_at IS NULL OR n.published_at <= CURRENT_TIMESTAMP)
              AND (n.expires_at IS NULL OR n.expires_at >= CURRENT_TIMESTAMP)
            ORDER BY n.priority ASC;
        ");

        // Vista: Eventos del mes actual
        DB::unprepared("
            CREATE OR REPLACE VIEW v_current_month_events AS
            SELECT
                e.id,
                e.company_id,
                e.title,
                e.description,
                e.content,
                e.event_date,
                e.start_time,
                e.end_time,
                e.is_all_day,
                e.color,
                EXTRACT(DAY FROM e.event_date) as day
            FROM calendar_events e
            WHERE e.deleted_at IS NULL
              AND e.status = 'active'
              AND EXTRACT(MONTH FROM e.event_date) = EXTRACT(MONTH FROM CURRENT_DATE)
              AND EXTRACT(YEAR FROM e.event_date) = EXTRACT(YEAR FROM CURRENT_DATE);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar vistas
        DB::unprepared('DROP VIEW IF EXISTS v_current_month_events');
        DB::unprepared('DROP VIEW IF EXISTS v_active_news');
        DB::unprepared('DROP VIEW IF EXISTS v_active_users');

        // Eliminar triggers
        $tables = [
            'companies',
            'company_configurations',
            'users',
            'modules',
            'calendar_events',
            'news',
            'contacts',
            'banner_images',
            'cache_settings'
        ];

        foreach ($tables as $table) {
            DB::unprepared("DROP TRIGGER IF EXISTS update_{$table}_updated_at ON {$table}");
        }

        DB::unprepared('DROP TRIGGER IF EXISTS ensure_super_admin_trigger ON users');
        DB::unprepared('DROP TRIGGER IF EXISTS check_admin_limit_trigger ON users');

        // Eliminar funciones
        DB::unprepared('DROP FUNCTION IF EXISTS ensure_super_admin()');
        DB::unprepared('DROP FUNCTION IF EXISTS check_admin_limit()');
        DB::unprepared('DROP FUNCTION IF EXISTS update_updated_at_column()');
    }
};
