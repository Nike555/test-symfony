<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Partner;
use App\Entity\Prize;
use App\Entity\User;
use App\Entity\UserGamePrize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<UserGamePrize>
 *
 * @method UserGamePrize|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGamePrize|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGamePrize[]    findAll()
 * @method UserGamePrize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGamePrizeRepository extends ServiceEntityRepository
{
    private $currentDay;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGamePrize::class);
        $this->currentDay = (new \DateTime())->format('Y-m-d');
    }

    public function save(UserGamePrize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserGamePrize $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function checkUserCanPlay(UserInterface $user): bool
    {
        $userCanPlay = $this->createQueryBuilder('ugp')
            ->select('COUNT(ugp.id)')
            ->leftJoin(User::class, 'u', Join::WITH, 'u.id = ugp.user')
            ->leftJoin(Game::class, 'g', Join::WITH, 'g.id = ugp.game')
            ->where(':current_day BETWEEN g.start_date AND g.end_date')
            ->andWhere('ugp.user = :user_id')
            ->setParameter('user_id', $user->getId())
            ->setParameter('current_day', $this->currentDay)
            ->getQuery()
            ->getSingleScalarResult();
        return !$userCanPlay;
    }

    public function getUserGamePrize(UserInterface $user): ?UserGamePrize
    {
        $userLanguageId = $user->getLanguage()->getId();

        $gameInfo = $this->createQueryBuilder('ugp')
            ->select('ugp, pt')
            ->leftJoin(User::class, 'u', Join::WITH, 'u.id = ugp.user')
            ->leftJoin(Game::class, 'g', Join::WITH, 'g.id = ugp.game')
            ->leftJoin(Prize::class, 'pr', Join::WITH, 'pr.id = ugp.prize')
            ->leftJoin(Partner::class, 'pt', Join::WITH, 'pt.code = pr.partner_code')
            ->where(':current_day BETWEEN g.start_date AND g.end_date')
            ->andWhere('ugp.user = :user_id')
            ->andWhere('pt.language = :language_id')
            ->setParameter('user_id', $user->getId())
            ->setParameter('current_day', $this->currentDay)
            ->setParameter('language_id', $userLanguageId)
            ->getQuery()
            ->execute();

        if ($gameInfo !== null) {
            if (!empty($gameInfo[1])) {
                $gameInfo[0]->partner = $gameInfo[1];
            }
            return $gameInfo[0];
        }
        return null;
    }
}
