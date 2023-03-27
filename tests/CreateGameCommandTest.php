<?php

namespace App\Tests;

use App\Command\NewGameCommand;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CreateGameCommandTest extends KernelTestCase
{
    private $command;

    protected function setUp(): void
    {
        //parent::setUp();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->gameRepositoryMock = $this->createMock(GameRepository::class);
    }

    /** @test */
    public function create_new_game()
    {
        $this->gameRepositoryMock->method('checkExistOnDateInterval')->willReturn(false);

        $input = new ArrayInput(['--name' => 'Test Game', '--date' => '2023-03-27']);
        $output = new BufferedOutput();

        $this->command = new NewGameCommand($this->entityManager);
        $this->command->run($input, $output);

        $this->assertTrue(true);
    }
}