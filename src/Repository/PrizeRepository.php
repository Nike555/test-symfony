<?php

namespace App\Repository;

use App\Entity\Prize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prize>
 *
 * @method Prize|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prize|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prize[]    findAll()
 * @method Prize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prize::class);
    }

    public function save(Prize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Prize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function totalPrizesByUserLanguage(int $languageId): int
    {
        return $this->count(['language' => $languageId]);
    }
}
