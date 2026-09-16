<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-demo',
    description: 'Seeds realistic demo users for the Foydalanuvchi loyihasi application',
)]
class SeedDemoDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('reset', null, InputOption::VALUE_NONE, 'Clear existing users before seeding');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $reset = $input->getOption('reset');

        $userRepository = $this->entityManager->getRepository(User::class);

        if ($reset) {
            $existing = $userRepository->findAll();
            foreach ($existing as $u) {
                $this->entityManager->remove($u);
            }
            $this->entityManager->flush();
            $io->note('Cleared all existing users.');
        }

        $demoUsers = [
            [
                'firstName' => 'Shuxrat',
                'lastName' => 'Maxmadaliev',
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'roles' => ['ROLE_ADMIN'],
                'company' => 'Astracode Tech',
                'position' => 'Lead Software Architect',
                'phone' => '+998 90 123 45 67',
                'status' => 'ACTIVE',
                'bio' => 'System architect and lead engineer at Astracode Tech with 10+ years of backend development experience.',
            ],
            [
                'firstName' => 'Dilnoza',
                'lastName' => 'Karimova',
                'email' => 'manager@example.com',
                'password' => 'manager123',
                'roles' => ['ROLE_MANAGER'],
                'company' => 'Astracode Tech',
                'position' => 'Product Director',
                'phone' => '+998 93 456 78 90',
                'status' => 'ACTIVE',
                'bio' => 'Oversees product strategy, user experience roadmaps, and cross-functional agile teams.',
            ],
            [
                'firstName' => 'Jamshid',
                'lastName' => 'Aliyev',
                'email' => 'user@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'FinSoft Innovations',
                'position' => 'Senior Full-Stack Engineer',
                'phone' => '+998 94 321 65 43',
                'status' => 'ACTIVE',
                'bio' => 'Specializes in reactive web frontends and high-load microservice APIs.',
            ],
            [
                'firstName' => 'Elena',
                'lastName' => 'Smirnova',
                'email' => 'elena.smirnova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_MANAGER'],
                'company' => 'Global Logistics Ltd',
                'position' => 'Operations Manager',
                'phone' => '+998 91 789 01 23',
                'status' => 'ACTIVE',
                'bio' => 'Coordinates regional supply chain workflows and digital transformation initiatives.',
            ],
            [
                'firstName' => 'Sardor',
                'lastName' => 'Raximov',
                'email' => 'sardor.raximov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Astracode Tech',
                'position' => 'Backend Developer',
                'phone' => '+998 97 111 22 33',
                'status' => 'ACTIVE',
                'bio' => 'Passionate about Symfony, API Platform, and event-driven architectures.',
            ],
            [
                'firstName' => 'Malika',
                'lastName' => 'Yusupova',
                'email' => 'malika.yusupova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'FinSoft Innovations',
                'position' => 'Frontend Developer',
                'phone' => '+998 90 999 88 77',
                'status' => 'ACTIVE',
                'bio' => 'Creates accessible, responsive web interfaces with modern CSS and JavaScript.',
            ],
            [
                'firstName' => 'Bobur',
                'lastName' => 'Mirzayev',
                'email' => 'bobur.mirzayev@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'TechUz Innovations',
                'position' => 'DevOps & Cloud Engineer',
                'phone' => '+998 99 555 44 33',
                'status' => 'ACTIVE',
                'bio' => 'Automates CI/CD pipelines, Kubernetes clusters, and cloud observability.',
            ],
            [
                'firstName' => 'Zarina',
                'lastName' => 'Ahmedova',
                'email' => 'zarina.ahmedova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Astracode Tech',
                'position' => 'UI/UX Designer',
                'phone' => '+998 93 777 66 55',
                'status' => 'PENDING',
                'bio' => 'Crafts user journey maps, design systems, and modern SaaS product interfaces.',
            ],
            [
                'firstName' => 'Otabek',
                'lastName' => 'Qodirov',
                'email' => 'otabek.qodirov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'TechUz Innovations',
                'position' => 'QA Automation Lead',
                'phone' => '+998 94 222 33 44',
                'status' => 'INACTIVE',
                'bio' => 'Builds end-to-end automated testing frameworks and ensures software quality standards.',
            ],
            [
                'firstName' => 'Nilufar',
                'lastName' => 'Toirova',
                'email' => 'nilufar.toirova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'FinSoft Innovations',
                'position' => 'Data Analyst',
                'phone' => '+998 98 444 55 66',
                'status' => 'ACTIVE',
                'bio' => 'Turns business data into actionable dashboards and predictive statistical models.',
            ],
            [
                'firstName' => 'Azizbek',
                'lastName' => 'Xolmatov',
                'email' => 'azizbek.xolmatov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Digital Systems',
                'position' => 'System Administrator',
                'phone' => '+998 90 333 22 11',
                'status' => 'PENDING',
                'bio' => 'Manages high-availability server infrastructure, security audits, and backups.',
            ],
            [
                'firstName' => 'Shahzod',
                'lastName' => 'Nurmatov',
                'email' => 'shahzod.nurmatov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Astracode Tech',
                'position' => 'Cybersecurity Analyst',
                'phone' => '+998 91 666 77 88',
                'status' => 'ACTIVE',
                'bio' => 'Focuses on penetration testing, identity management, and API access security.',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($demoUsers as $data) {
            $user = $userRepository->findOneBy(['email' => $data['email']]);
            $isNew = false;

            if (!$user) {
                $user = new User();
                $isNew = true;
                $user->setEmail($data['email']);
            }

            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setRoles($data['roles']);
            $user->setCompany($data['company']);
            $user->setPosition($data['position']);
            $user->setPhone($data['phone']);
            $user->setStatus($data['status']);
            $user->setBio($data['bio']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);

            if ($isNew) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            'Demo data successfully seeded! Created: %d, Updated: %d. Total users in database: %d',
            $created,
            $updated,
            count($userRepository->findAll())
        ));

        $io->table(
            ['Role', 'Email', 'Password', 'Full Name', 'Company', 'Status'],
            [
                ['ROLE_ADMIN', 'admin@example.com', 'admin123', 'Shuxrat Maxmadaliev', 'Astracode Tech', 'ACTIVE'],
                ['ROLE_MANAGER', 'manager@example.com', 'manager123', 'Dilnoza Karimova', 'Astracode Tech', 'ACTIVE'],
                ['ROLE_USER', 'user@example.com', 'user123', 'Jamshid Aliyev', 'FinSoft Innovations', 'ACTIVE'],
            ]
        );

        return Command::SUCCESS;
    }
}
