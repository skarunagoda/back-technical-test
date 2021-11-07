<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->testFixtureOrderCheckOK($manager);
        $this->testFixtureOrderCheckHeavyAndForeign($manager);
        $this->testFixtureOrderCheckEmptyMail($manager);
        $this->testFixtureOrderCheckExceed60kg($manager);
        $this->testFixtureOrderCheckInvalidFrenchAddress($manager);

        for ($i = 1; $i < 30; $i++) {
            $order = new Order();
            $order->setContactEmail($this->faker->randomElement(['', $this->faker->email]));
            $order->setName('#'. mt_rand(10, 100000));
            $order->setShippingAddress('8 rue de la paix');
            $order->setShippingZipcode((string) (mt_rand(10, 95) * 1000));
            $order->setShippingCountry($this->faker->randomElement(['France', $this->faker->country]));

            $nbLines = mt_rand(1, 10);
            for ($j = 1; $j < $nbLines; $j++) {
                $qty = mt_rand(1, 4);
                $price = mt_rand(100, 5000);

                $orderLine = new OrderLine();
                $orderLine->setQuantity($qty);
                $orderLine->setTotal($qty * $price);

                $product = new Product();
                $product->setName($this->faker->safeColorName . ' nuts');
                $product->setWeight(mt_rand(100, 5000));
                $manager->persist($product);

                $orderLine->setProduct($product);
                $orderLine->setOrder($order);

                $manager->persist($product);
                $manager->persist($orderLine);
            }

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function testFixtureOrderCheckOK(ObjectManager $manager){
        $order = new Order();
        $order->setId(1);
        $order->setContactEmail($this->faker->randomElement([$this->faker->email]));
        $order->setName('#'. mt_rand(10, 100000));
        $order->setShippingAddress('8 rue de la paix');
        $order->setShippingZipcode('75002');
        $order->setShippingCountry('France');

        $nbLines = 1;
        for ($j = 1; $j < $nbLines; $j++) {
            $price = 100;

            $orderLine = new OrderLine();
            $orderLine->setQuantity(10);
            $orderLine->setTotal($qty * $price);

            $product = new Product();
            $product->setName($this->faker->safeColorName . ' nuts');
            $product->setWeight(1000);
            $manager->persist($product);

            $orderLine->setProduct($product);
            $orderLine->setOrder($order);

            $manager->persist($product);
            $manager->persist($orderLine);
        }

        $manager->persist($order);
    }

    public function testFixtureOrderCheckHeavyAndForeign(ObjectManager $manager){
        $order = new Order();
        $order->setId(2);
        $order->setContactEmail($this->faker->randomElement([$this->faker->email]));
        $order->setName('#'. mt_rand(10, 100000));
        $order->setShippingAddress('8 rue de la paix');
        $order->setShippingZipcode('75002');
        $order->setShippingCountry('USA');

        $nbLines = 1;
        for ($j = 1; $j < $nbLines; $j++) {
            $price = 100;

            $orderLine = new OrderLine();
            $orderLine->setQuantity(10);
            $orderLine->setTotal($qty * $price);

            $product = new Product();
            $product->setName($this->faker->safeColorName . ' nuts');
            $product->setWeight(5000);
            $manager->persist($product);

            $orderLine->setProduct($product);
            $orderLine->setOrder($order);

            $manager->persist($product);
            $manager->persist($orderLine);
        }

        $manager->persist($order);
    }

    public function testFixtureOrderCheckEmptyMail(ObjectManager $manager){
        $order = new Order();
        $order->setId(3);
        $order->setContactEmail('');
        $order->setName('#'. mt_rand(10, 100000));
        $order->setShippingAddress('8 rue de la paix');
        $order->setShippingZipcode('75002');
        $order->setShippingCountry('France');

        $nbLines = 1;
        for ($j = 1; $j < $nbLines; $j++) {
            $price = 100;

            $orderLine = new OrderLine();
            $orderLine->setQuantity(10);
            $orderLine->setTotal($qty * $price);

            $product = new Product();
            $product->setName($this->faker->safeColorName . ' nuts');
            $product->setWeight(1000);
            $manager->persist($product);

            $orderLine->setProduct($product);
            $orderLine->setOrder($order);

            $manager->persist($product);
            $manager->persist($orderLine);
        }

        $manager->persist($order);
    }

    public function testFixtureOrderCheckExceed60kg(ObjectManager $manager){
        $order = new Order();
        $order->setId(4);
        $order->setContactEmail($this->faker->randomElement([$this->faker->email]));
        $order->setName('#'. mt_rand(10, 100000));
        $order->setShippingAddress('8 rue de la paix');
        $order->setShippingZipcode('75002');
        $order->setShippingCountry('France');

        $nbLines = 1;
        for ($j = 1; $j < $nbLines; $j++) {
            $price = 100;

            $orderLine = new OrderLine();
            $orderLine->setQuantity(10);
            $orderLine->setTotal($qty * $price);

            $product = new Product();
            $product->setName($this->faker->safeColorName . ' nuts');
            $product->setWeight(7000);
            $manager->persist($product);

            $orderLine->setProduct($product);
            $orderLine->setOrder($order);

            $manager->persist($product);
            $manager->persist($orderLine);
        }

        $manager->persist($order);
    }

    public function testFixtureOrderCheckInvalidFrenchAddress(ObjectManager $manager){
        $order = new Order();
        $order->setId(5);
        $order->setContactEmail($this->faker->randomElement([$this->faker->email]));
        $order->setName('#'. mt_rand(10, 100000));
        $order->setShippingAddress('8 rue de la paix');
        $order->setShippingZipcode('95600');
        $order->setShippingCountry('France');

        $nbLines = 1;
        for ($j = 1; $j < $nbLines; $j++) {
            $price = 100;

            $orderLine = new OrderLine();
            $orderLine->setQuantity(10);
            $orderLine->setTotal($qty * $price);

            $product = new Product();
            $product->setName($this->faker->safeColorName . ' nuts');
            $product->setWeight(1000);
            $manager->persist($product);

            $orderLine->setProduct($product);
            $orderLine->setOrder($order);

            $manager->persist($product);
            $manager->persist($orderLine);
        }

        $manager->persist($order);
    }
}
