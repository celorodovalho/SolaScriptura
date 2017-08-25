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
        // $this->call(UsersTableSeeder::class);
        $this->call(BooksTableSeeder::class);
        \DB::table('verses')->delete();
        $this->call(VersesTableAASeeder::class);
        $this->call(VersesTableACFSeeder::class);
        $this->call(VersesTableKJVSeeder::class);
        $this->call(VersesTableNVISeeder::class);
    }
}
