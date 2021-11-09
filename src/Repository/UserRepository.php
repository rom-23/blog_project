<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @throws ORMException
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return array
     */
    public function findAllUsers(): Query
    {
        $sql = "
                SELECT
                  partial e.{id,email,roles,image,password,isVerified,accountMustBeVerifiedBefore,registeredAt},
                  partial pos.{id, title},
                  partial not.{id, title}
            FROM App\Entity\User e
            LEFT JOIN e.posts pos
            LEFT JOIN e.notes not
        ";
        return $this->getEntityManager()->createQuery($sql);
    }

    /**
     * @return array
     */
    public function findUser($userId): Array
    {
        $aParameter = [];
        $sql = "
                SELECT
                  partial e.{id,email,roles,image,password,isVerified,accountMustBeVerifiedBefore,registeredAt},
                  partial pos.{id, title},
                  partial not.{id, title},
                  partial adr.{id, name}
            FROM App\Entity\User e
            LEFT JOIN e.posts pos
            LEFT JOIN e.notes not
            LEFT JOIN e.addresses adr
             WHERE e.id = :userId
        ";
        $aParameter = [
            'userId' => $userId
        ];
        return $this->getEntityManager()->createQuery($sql)->setParameters($aParameter)->getResult();
    }

}
