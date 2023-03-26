<?php

namespace App\Repository;

use App\Entity\Prize;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function getPrizeWithoutWins(UserInterface $currentUser): Prize
    {
        // Avoid RAND()
        $getMaxPrizeId = $this->createQueryBuilder('p')
            ->select('MAX(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $randomId = rand(1, $getMaxPrizeId);

        $randomPrize = $this->createQueryBuilder('p')
            ->where('p.id > :random_id')
            ->andWhere('p.won = :won')
            ->setParameter('random_id', $randomId)
            ->setParameter('won', false)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        // In case if random prize has another language
        if ($randomPrize->getLanguage()->getId() != $currentUser->getLanguage()->getId()) {
            return $this->findOneBy(['language' => $currentUser->getLanguage()->getId()]);
        }
        return $randomPrize;
    }

    public function setWonPrize(int $prizeUniqueCode)
    {
        $q = $this->getEntityManager()
            ->createQuery("UPDATE App\Entity\Prize p SET p.won = 1 WHERE p.unique_code = :unique_code")
            ->setParameter(':unique_code', $prizeUniqueCode);
        $q->execute();
    }
}
