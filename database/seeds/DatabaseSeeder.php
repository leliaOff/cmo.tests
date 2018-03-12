<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
    }
}

class RolesTableSeeder extends Seeder {
    
    public function run()
    {
        DB::table('roles')->delete();
        DB::table('roles')->insert(['alias' => 'user', 'title' => 'Пользователь']);
        DB::table('roles')->insert(['alias' => 'admin', 'title' => 'Администратор']);
        
    }

}
