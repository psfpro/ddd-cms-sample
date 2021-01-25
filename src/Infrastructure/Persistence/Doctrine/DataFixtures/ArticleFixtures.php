<?php

namespace App\Infrastructure\Persistence\Doctrine\DataFixtures;

use App\Domain\Article\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 123; $i++) {
            $manager->persist(Article::create(
                Uuid::v4(),
                $faker->text(100),
                $faker->text,
                new \DateTimeImmutable()
            ));
        }

        $manager->flush();
    }
}
