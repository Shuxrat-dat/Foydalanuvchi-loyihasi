<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function save(User $user, bool $flush = false): void
    {
        $this->getEntityManager()->persist($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $user, bool $flush = false): void
    {
        $this->getEntityManager()->remove($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return User[]
     */
    public function searchAndFilter(?string $query = null, ?string $role = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC');

        if ($query) {
            $qb->andWhere('LOWER(u.firstName) LIKE :q OR LOWER(u.lastName) LIKE :q OR LOWER(u.email) LIKE :q OR LOWER(u.company) LIKE :q OR LOWER(u.position) LIKE :q')
               ->setParameter('q', '%' . mb_strtolower(trim($query)) . '%');
        }

        if ($role && $role !== 'ALL') {
            $qb->andWhere('u.roles LIKE :role')
               ->setParameter('role', '%' . $role . '%');
        }

        if ($status && $status !== 'ALL') {
            $qb->andWhere('u.status = :status')
               ->setParameter('status', $status);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array{total: int, active: int, admins: int, pending: int}
     */
    public function getStatistics(): array
    {
        $all = $this->findAll();
        $total = count($all);
        $active = 0;
        $admins = 0;
        $pending = 0;

        foreach ($all as $user) {
            if ($user->getStatus() === 'ACTIVE') {
                $active++;
            } elseif ($user->getStatus() === 'PENDING') {
                $pending++;
            }

            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                $admins++;
            }
        }

        return [
            'total' => $total,
            'active' => $active,
            'admins' => $admins,
            'pending' => $pending,
        ];
    }
}
