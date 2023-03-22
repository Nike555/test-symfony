<?php

namespace App\DataFixtures;

use App\Entity\Language;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LanguageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $language = new Language();
        $language->setCode('en');
        $language->setName('English');
        $manager->persist($language);

        $language2 = new Language();
        $language2->setCode('de');
        $language2->setName('Deutsch');
        $manager->persist($language2);

        $manager->flush();
    }
}
