<?php

namespace Database\Seeders;

use App\Models\{Category, Customer, Item, User};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Initial users (password for all: "password")
        User::create(['name' => 'Olivia Owner',   'email' => 'owner@icecream.test',   'password' => 'password', 'role' => 'owner']);
        User::create(['name' => 'Mark Manager',   'email' => 'manager@icecream.test', 'password' => 'password', 'role' => 'manager']);
        User::create(['name' => 'Cathy Cashier',  'email' => 'cashier@icecream.test', 'password' => 'password', 'role' => 'cashier']);

        // Flavors (categories)
        $flavors = collect(['Chocolate', 'Vanilla', 'Strawberry'])
            ->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n])]);

        // Inventory
        Item::create(['category_id' => $flavors['Chocolate']->id,  'name' => 'Choco Fudge Cone',     'description' => 'Rich chocolate cone',   'price' => 3.50, 'quantity' => 50]);
        Item::create(['category_id' => $flavors['Vanilla']->id,    'name' => 'Vanilla Bean Cup',     'description' => 'Classic vanilla cup',   'price' => 3.00, 'quantity' => 40]);
        Item::create(['category_id' => $flavors['Strawberry']->id, 'name' => 'Strawberry Swirl Tub', 'description' => 'Family size tub',      'price' => 8.75, 'quantity' => 15]);

        // Customers
        Customer::create(['name' => 'Nimal Perera', 'email' => 'nimal@example.com', 'phone' => '0771234567', 'address' => 'Negombo']);
        Customer::create(['name' => 'Kasun Silva',  'email' => 'kasun@example.com', 'phone' => '0712345678', 'address' => 'Colombo']);
    }
}
