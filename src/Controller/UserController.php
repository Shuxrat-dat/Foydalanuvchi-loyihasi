<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route(path: '/', name: 'app_home', methods: ['GET'])]
    #[Route(path: '/users', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $query = $request->query->get('q', '');
        $role = $request->query->get('role', 'ALL');
        $status = $request->query->get('status', 'ALL');

        $users = $userRepository->searchAndFilter(
            query: $query ?: null,
            role: $role ?: null,
            status: $status ?: null,
        );

        $stats = $userRepository->getStatistics();

        if ($request->isXmlHttpRequest() || $request->query->getBoolean('ajax')) {
            $data = array_map(fn (User $u) => $this->serializeUser($u), $users);

            return new JsonResponse([
                'success' => true,
                'users' => $data,
                'stats' => $stats,
            ]);
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'stats' => $stats,
            'query' => $query,
            'current_role' => $role,
            'current_status' => $status,
        ]);
    }

    #[Route(path: '/users/{id}', name: 'app_user_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'user' => $this->serializeUser($user),
        ]);
    }

    #[Route(path: '/users/new', name: 'app_user_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator,
        UserRepository $userRepository
    ): Response {
        $payload = $this->extractPayload($request);

        $email = mb_strtolower(trim($payload['email'] ?? ''));
        $firstName = trim($payload['firstName'] ?? '');
        $lastName = trim($payload['lastName'] ?? '');
        $plainPassword = $payload['password'] ?? 'user123';
        $role = $payload['role'] ?? 'ROLE_USER';
        $status = $payload['status'] ?? 'ACTIVE';
        $phone = trim($payload['phone'] ?? '');
        $company = trim($payload['company'] ?? '');
        $position = trim($payload['position'] ?? '');
        $bio = trim($payload['bio'] ?? '');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse($request, 'Iltimos, toʻgʻri email manzilini kiriting.');
        }

        if (!$firstName || !$lastName) {
            return $this->errorResponse($request, 'Ism va familiya maydonlari toʻldirilishi shart.');
        }

        if ($userRepository->findOneBy(['email' => $email])) {
            return $this->errorResponse($request, 'Ushbu email manzili bilan foydalanuvchi allaqachon mavjud.');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles([$role]);
        $user->setStatus($status);
        $user->setPhone($phone ?: null);
        $user->setCompany($company ?: null);
        $user->setPosition($position ?: null);
        $user->setBio($bio ?: null);
        $user->setPassword($hasher->hashPassword($user, $plainPassword ?: 'user123'));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->errorResponse($request, (string) $errors->get(0)->getMessage());
        }

        $em->persist($user);
        $em->flush();

        if ($this->isJsonExpected($request)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Foydalanuvchi muvaffaqiyatli yaratildi!',
                'user' => $this->serializeUser($user),
            ], Response::HTTP_CREATED);
        }

        $this->addFlash('success', 'Foydalanuvchi muvaffaqiyatli yaratildi!');
        return $this->redirectToRoute('app_user_index');
    }

    #[Route(path: '/users/{id}/edit', name: 'app_user_edit', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ValidatorInterface $validator,
        UserRepository $userRepository
    ): Response {
        $payload = $this->extractPayload($request);

        $email = mb_strtolower(trim($payload['email'] ?? $user->getEmail()));
        $firstName = trim($payload['firstName'] ?? $user->getFirstName());
        $lastName = trim($payload['lastName'] ?? $user->getLastName());
        $role = $payload['role'] ?? $user->getPrimaryRole();
        $status = $payload['status'] ?? $user->getStatus();
        $phone = isset($payload['phone']) ? trim($payload['phone']) : $user->getPhone();
        $company = isset($payload['company']) ? trim($payload['company']) : $user->getCompany();
        $position = isset($payload['position']) ? trim($payload['position']) : $user->getPosition();
        $bio = isset($payload['bio']) ? trim($payload['bio']) : $user->getBio();

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse($request, 'Iltimos, toʻgʻri email manzilini kiriting.');
        }

        if (!$firstName || !$lastName) {
            return $this->errorResponse($request, 'Ism va familiya maydonlari toʻldirilishi shart.');
        }

        $existingUser = $userRepository->findOneBy(['email' => $email]);
        if ($existingUser && $existingUser->getId() !== $user->getId()) {
            return $this->errorResponse($request, 'Ushbu email manzili bilan foydalanuvchi allaqachon mavjud.');
        }

        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles([$role]);
        $user->setStatus($status);
        $user->setPhone($phone ?: null);
        $user->setCompany($company ?: null);
        $user->setPosition($position ?: null);
        $user->setBio($bio ?: null);
        $user->setUpdatedAt(new \DateTimeImmutable());

        if (!empty($payload['password'])) {
            $user->setPassword($hasher->hashPassword($user, $payload['password']));
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->errorResponse($request, (string) $errors->get(0)->getMessage());
        }

        $em->flush();

        if ($this->isJsonExpected($request)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Foydalanuvchi maʼlumotlari muvaffaqiyatli yangilandi!',
                'user' => $this->serializeUser($user),
            ]);
        }

        $this->addFlash('success', 'Foydalanuvchi maʼlumotlari muvaffaqiyatli yangilandi!');
        return $this->redirectToRoute('app_user_index');
    }

    #[Route(path: '/users/{id}/delete', name: 'app_user_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $userId = $user->getId();
        $em->remove($user);
        $em->flush();

        if ($this->isJsonExpected($request)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Foydalanuvchi muvaffaqiyatli oʻchirildi!',
                'deletedId' => $userId,
            ]);
        }

        $this->addFlash('success', 'Foydalanuvchi muvaffaqiyatli oʻchirildi!');
        return $this->redirectToRoute('app_user_index');
    }

    #[Route(path: '/users/reset-demo', name: 'app_user_reset_demo', methods: ['POST'])]
    public function resetDemo(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $repo = $em->getRepository(User::class);
        $existing = $repo->findAll();
        foreach ($existing as $u) {
            $em->remove($u);
        }
        $em->flush();

        // Reseed
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

        foreach ($demoUsers as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setRoles($data['roles']);
            $user->setCompany($data['company']);
            $user->setPosition($data['position']);
            $user->setPhone($data['phone']);
            $user->setStatus($data['status']);
            $user->setBio($data['bio']);
            $user->setPassword($hasher->hashPassword($user, $data['password']));

            $em->persist($user);
        }

        $em->flush();

        if ($this->isJsonExpected($request)) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Demo maʼlumotlar 12 ta foydalanuvchi bilan tiklandi!',
            ]);
        }

        $this->addFlash('success', 'Demo maʼlumotlar muvaffaqiyatli tiklandi!');
        return $this->redirectToRoute('app_user_index');
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeUser(User $u): array
    {
        return [
            'id' => $u->getId(),
            'email' => $u->getEmail(),
            'firstName' => $u->getFirstName(),
            'lastName' => $u->getLastName(),
            'fullName' => $u->getFullName(),
            'initials' => $u->getInitials(),
            'roles' => $u->getRoles(),
            'role' => $u->getPrimaryRole(),
            'roleLabel' => $u->getPrimaryRoleLabel(),
            'company' => $u->getCompany(),
            'position' => $u->getPosition(),
            'phone' => $u->getPhone(),
            'status' => $u->getStatus(),
            'bio' => $u->getBio(),
            'createdAt' => $u->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $u->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractPayload(Request $request): array
    {
        $contentType = (string) $request->headers->get('Content-Type');
        if (str_contains($contentType, 'application/json')) {
            $content = (string) $request->getContent();
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $request->request->all();
    }

    private function isJsonExpected(Request $request): bool
    {
        return $request->isXmlHttpRequest()
            || str_contains((string) $request->headers->get('Accept'), 'application/json')
            || str_contains((string) $request->headers->get('Content-Type'), 'application/json');
    }

    private function errorResponse(Request $request, string $message): Response
    {
        if ($this->isJsonExpected($request)) {
            return new JsonResponse([
                'success' => false,
                'message' => $message,
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->addFlash('error', $message);
        return $this->redirectToRoute('app_user_index');
    }
}
