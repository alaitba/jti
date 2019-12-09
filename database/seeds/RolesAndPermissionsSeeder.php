<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Сбрасываем кэш ролей и прав
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * Создаем права
         */

        // Администраторы
        Permission::create(['name' => 'view admins', 'guard_name' => 'admin']);
        Permission::create(['name' => 'create admins', 'guard_name' => 'admin']);
        Permission::create(['name' => 'update admins', 'guard_name' => 'admin']);
        Permission::create(['name' => 'edit admins', 'guard_name' => 'admin']);
        Permission::create(['name' => 'delete admins', 'guard_name' => 'admin']);

        // Шаблоны писем
        Permission::create(['name' => 'view email-templates', 'guard_name' => 'admin']);
        Permission::create(['name' => 'create email-templates', 'guard_name' => 'admin']);
        Permission::create(['name' => 'update email-templates', 'guard_name' => 'admin']);
        Permission::create(['name' => 'edit email-templates', 'guard_name' => 'admin']);
        Permission::create(['name' => 'delete email-templates', 'guard_name' => 'admin']);

        // Локализация
        Permission::create(['name' => 'view localizations', 'guard_name' => 'admin']);
        Permission::create(['name' => 'create localizations', 'guard_name' => 'admin']);
        Permission::create(['name' => 'update localizations', 'guard_name' => 'admin']);
        Permission::create(['name' => 'edit localizations', 'guard_name' => 'admin']);
        Permission::create(['name' => 'delete localizations', 'guard_name' => 'admin']);


        /**
         * Создаем роли и присваиваем права
         */

        // Менеджер
        $role = Role::create(['name' => 'manager', 'guard_name' => 'admin']);
        $role->givePermissionTo('view localizations',
            'create localizations',
            'update localizations',
            'edit localizations',
            'delete localizations'
        );

        // Админ
        $role = Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
