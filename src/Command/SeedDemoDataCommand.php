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
    description: 'Foydalanuvchi loyihasi ilovasi uchun demo foydalanuvchilarni yuklash',
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
        $this->addOption('reset', null, InputOption::VALUE_NONE, 'Yuklashdan oldin mavjud foydalanuvchilarni oʻchirish');
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
            $io->note('Barcha mavjud foydalanuvchilar oʻchirildi.');
        }

        $demoUsers = [
            [
                'firstName' => 'Shuxrat',
                'lastName' => 'Maxmadaliev',
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'roles' => ['ROLE_ADMIN'],
                'company' => 'Astracode Tech',
                'position' => 'Bosh dasturiy taʼminot arxitektori',
                'phone' => '+998 90 123 45 67',
                'status' => 'ACTIVE',
                'bio' => 'Astracode Tech da tizim arxitektori va bosh muhandis, 10+ yillik backend ishlab chiqish tajribasiga ega.',
            ],
            [
                'firstName' => 'Dilnoza',
                'lastName' => 'Karimova',
                'email' => 'manager@example.com',
                'password' => 'manager123',
                'roles' => ['ROLE_MANAGER'],
                'company' => 'Astracode Tech',
                'position' => 'Mahsulot direktori',
                'phone' => '+998 93 456 78 90',
                'status' => 'ACTIVE',
                'bio' => 'Mahsulot strategiyasi, foydalanuvchi tajribasi yoʻl xaritalari va turkich funktsiyali jamoalarni boshqaradi.',
            ],
            [
                'firstName' => 'Jamshid',
                'lastName' => 'Aliyev',
                'email' => 'user@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'FinSoft Innovations',
                'position' => 'Katta Full-Stack muhandisi',
                'phone' => '+998 94 321 65 43',
                'status' => 'ACTIVE',
                'bio' => 'Reaktiv veb interfeyslar va yuqori yukli mikroservis APIlar boʻyicha ixtisoslashgan.',
            ],
            [
                'firstName' => 'Elena',
                'lastName' => 'Smirnova',
                'email' => 'elena.smirnova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_MANAGER'],
                'company' => 'Global Logistics Ltd',
                'position' => 'Operatsiyalar menejeri',
                'phone' => '+998 91 789 01 23',
                'status' => 'ACTIVE',
                'bio' => 'Mintaqaviy taʼminot zanjiri jarayonlari va raqamli transformatsiya loyihalarini muvofiqlashtiradi.',
            ],
            [
                'firstName' => 'Sardor',
                'lastName' => 'Raximov',
                'email' => 'sardor.raximov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Astracode Tech',
                'position' => 'Backend dasturchi',
                'phone' => '+998 97 111 22 33',
                'status' => 'ACTIVE',
                'bio' => 'Symfony, API Platform va hodisaga asoslangan arxitekturalar qiziqadi.',
            ],
            [
                'firstName' => 'Malika',
                'lastName' => 'Yusupova',
                'email' => 'malika.yusupova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'FinSoft Innovations',
                'position' => 'Frontend dasturchi',
                'phone' => '+998 90 999 88 77',
                'status' => 'ACTIVE',
                'bio' => 'Zamonaviy CSS va JavaScript yordamida qulay, moslashuvchan veb interfeyslar yaratadi.',
            ],
            [
                'firstName' => 'Bobur',
                'lastName' => 'Mirzayev',
                'email' => 'bobur.mirzayev@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'TechUz Innovations',
                'position' => 'DevOps va Cloud muhandisi',
                'phone' => '+998 99 555 44 33',
                'status' => 'ACTIVE',
                'bio' => 'CI/CD konveyerlarini, Kubernetes klasterlarini va bulut kuzatuvini avtomatlashtiradi.',
            ],
            [
                'firstName' => 'Zarina',
                'lastName' => 'Ahmedova',
                'email' => 'zarina.ahmedova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Astracode Tech',
                'position' => 'UI/UX dizayner',
                'phone' => '+998 93 777 66 55',
                'status' => 'PENDING',
                'bio' => 'Foydalanuvchi sayohat xaritalari, dizayn tizimlari va zamonaviy SaaS mahsulot interfeyslarini ishlab chiqadi.',
            ],
            [
                'firstName' => 'Otabek',
                'lastName' => 'Qodirov',
                'email' => 'otabek.qodirov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'TechUz Innovations',
                'position' => 'QA Avtomatlashtirish boshligʻi',
                'phone' => '+998 94 222 33 44',
                'status' => 'INACTIVE',
                'bio' => 'Toʻliq avtomatlashtirilgan testlash tizimlarini quradi va dasturiy taʼminot sifatini taʼminlaydi.',
            ],
            [
                'firstName' => 'Nilufar',
                'lastName' => 'Toirova',
                'email' => 'nilufar.toirova@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'FinSoft Innovations',
                'position' => 'Maʼlumotlar tahlilchisi',
                'phone' => '+998 98 444 55 66',
                'status' => 'ACTIVE',
                'bio' => 'Biznes maʼlumotlarini amaliy dashboardlarga va bashorat qiluvchi statistik modellarga aylantiradi.',
            ],
            [
                'firstName' => 'Azizbek',
                'lastName' => 'Xolmatov',
                'email' => 'azizbek.xolmatov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Digital Systems',
                'position' => 'Tizim administratori',
                'phone' => '+998 90 333 22 11',
                'status' => 'PENDING',
                'bio' => 'Yuqori mavjud server infratuzilmasini, xavfsizlik auditlarini va zaxira nusxalarini boshqaradi.',
            ],
            [
                'firstName' => 'Shahzod',
                'lastName' => 'Nurmatov',
                'email' => 'shahzod.nurmatov@example.com',
                'password' => 'user123',
                'roles' => ['ROLE_USER'],
                'company' => 'Astracode Tech',
                'position' => 'Kiberxavfsizlik tahlilchisi',
                'phone' => '+998 91 666 77 88',
                'status' => 'ACTIVE',
                'bio' => 'Penetratsiyani sinovdan oʻtkazish, identifikatorlarni boshqarish va API kirish xavfsizligiga eʼtibor qaratadi.',
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
            'Demo maʼlumotlar muvaffaqiyatli yuklandi! Yaratilgan: %d, Yangilangan: %d. Baza jami foydalanuvchilar: %d',
            $created,
            $updated,
            count($userRepository->findAll())
        ));

        $io->table(
            ['Rol', 'Email', 'Parol', 'Toʻliq ism', 'Korxona', 'Holat'],
            [
                ['ROLE_ADMIN', 'admin@example.com', 'admin123', 'Shuxrat Maxmadaliev', 'Astracode Tech', 'ACTIVE'],
                ['ROLE_MANAGER', 'manager@example.com', 'manager123', 'Dilnoza Karimova', 'Astracode Tech', 'ACTIVE'],
                ['ROLE_USER', 'user@example.com', 'user123', 'Jamshid Aliyev', 'FinSoft Innovations', 'ACTIVE'],
            ]
        );

        return Command::SUCCESS;
    }
}
