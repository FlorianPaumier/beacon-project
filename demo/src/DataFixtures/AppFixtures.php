<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setName('Admin User');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $manager->persist($admin);

        $electronics = new Category();
        $electronics->setName('Electronics');
        $electronics->setSlug('electronics');
        $manager->persist($electronics);

        $clothing = new Category();
        $clothing->setName('Clothing');
        $clothing->setSlug('clothing');
        $manager->persist($clothing);

        $books = new Category();
        $books->setName('Books');
        $books->setSlug('books');
        $manager->persist($books);

        $products = [
            ['iPhone 15', 'Latest Apple smartphone', 999.99, $electronics],
            ['MacBook Pro', '14-inch M3 laptop', 1999.99, $electronics],
            ['AirPods Pro', 'Wireless earbuds', 249.99, $electronics],
            ['Cotton T-Shirt', 'Classic white tee', 29.99, $clothing],
            ['Denim Jacket', 'Blue denim jacket', 89.99, $clothing],
            ['Running Shoes', 'Lightweight sneakers', 119.99, $clothing],
            ['Symfony 7 Guide', 'Web development book', 49.99, $books],
            ['PHP 8.4 Cookbook', 'Modern PHP recipes', 39.99, $books],
        ];

        foreach ($products as [$name, $description, $price, $category]) {
            $product = new Product();
            $product->setName($name);
            $product->setDescription($description);
            $product->setPrice((string) $price);
            $product->setCategory($category);
            $product->setIsActive(true);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
