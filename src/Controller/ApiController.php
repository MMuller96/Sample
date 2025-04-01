<?php

namespace App\Controller;

use App\Constants\CalculationConstants;
use App\Entity\Calculation;
use App\Repository\CalculationRepository;
use App\Service\CalculationService;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CalculationService $calculationService,
        private CalculationRepository $calculationRepository,
        private SerializerInterface $serializer,
    ){}

    #[Route('/api/calculate', name: 'api_calculate', methods: ['POST'])]
    public function calculate(Request $request): JsonResponse
    {
        $amount = $request->get('amount');
        $installments = $request->get('installments');
        $date = new \DateTime();

        if ($amount < 1000 || $amount > 12000 || $amount % 500 !== 0) {
            return new JsonResponse(['error' => 'Invalid amount'], 400);
        }
        if ($installments < 3 || $installments > 18 || $installments % 3 !== 0) {
            return new JsonResponse(['error' => 'Invalid installments count'], 400);
        }

        $calculation = (new Calculation())
            ->setAmount($amount)
            ->setInstallments($installments)
            ->setCreatedAt($date)
            ->setSchedule($this->calculationService->calculateSchedule($amount, $installments));

        $this->em->persist($calculation);
        $this->em->flush();
        
        return new JsonResponse([
            'calculation metric' => [
                'date' => $date,
                'installments' => $installments,
                'amount' => $amount,
                'interest_rate' => $calculation->getInterestRate()
            ],
            'schedule' => $calculation->getSchedule()
        ]);
    }

    #[Route('/api/exclude', name: 'api_exclude', methods: ['PUT'])]
    public function exclude(Request $request): JsonResponse
    {
        $id = $request->get('id');

        if($id === null) return new JsonResponse(['error' => 'Invalid id'], 400);

        $calculation = $this->calculationRepository->findOneBy(['id' => $id]);

        if($calculation === null) return new JsonResponse(['warning' => 'Calculation not exists'], 404);

        $calculation->setExcluded(true);
        $this->em->flush();

        return new JsonResponse(['message' => 'Calculation nr.' . $id . ' excluded succesfully']);
    }

    #[Route('/api/calculations', name: 'api_calculations', methods: ['GET'])]
    public function calculations(Request $request): JsonResponse
    {
        $filter = $request->query->get('filter');
        $result = $this->calculationRepository->getTop4($filter === 'excluded');
        $data = [];

        foreach($result as $row)
        {
            $data[] = [
                'id' => $row['id'],
                'amount' => $row['amount'],
                'installments' => $row['installments'],
                'interest_rate' => $row['interest_rate'],
                'created_at' => $row['created_at'],
                'excluded' => $row['excluded'],
                'schedule' => json_decode($row['schedule']),
            ];
        }

        return new JsonResponse($data);
    }
}
