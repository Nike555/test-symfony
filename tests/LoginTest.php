<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoginTest extends KernelTestCase
{
    protected function setUp(): void
    {
        //parent::setUp();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testItWorks()
    {
        $this->assertTrue(true);
    }
}