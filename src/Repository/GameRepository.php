<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Prize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function save(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkExistOnDateInterval(\DateTimeInterface $startDate, \DateTimeInterface $endDate): bool
    {
        $existGame = $this->createQueryBuilder('g')
            ->where('g.start_date BETWEEN :start AND :end')
            ->orWhere('g.end_date BETWEEN :start AND :end')
            ->setParameter('start', $startDate->format('Y-m-d'))
            ->setParameter('end', $endDate->format('Y-m-d'))
            ->getQuery()
            ->execute();
        return (bool)$existGame;
    }

    public function currentGameCountDays(): int
    {
        $currentGameDates = $this->createQueryBuilder('g')
            ->select('g.start_date', 'g.end_date')
            ->where('CURRENT_DATE() BETWEEN g.start_date AND g.end_date')
            ->getQuery()
            ->getOneOrNullResult();

        $startDay = new \DateTime($currentGameDates['start_date']->format('Y-m-d'));
        $endDay = new \DateTime($currentGameDates['end_date']->format('Y-m-d'));
        $interval = $endDay->diff($startDay);
        return $interval->days + 1; // Adding 1 to include both the start and end dates
    }
}
