<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\ExcursionsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    #[Route('/download-receipt/{excursionId}', name: 'app_download_receipt')]
    public function downloadReceipt(int $excursionId, ExcursionsRepository $excursionsRepository): Response
    {
        // 1. Récupérer l'excursion
        $excursion = $excursionsRepository->find($excursionId);

        if (!$excursion) {
            throw $this->createNotFoundException('Excursion non trouvée');
        }

        // 2. Générer le HTML
        $html = $this->renderView('pdf/receipt.html.twig', [
            'excursion' => $excursion,
        ]);

        // 3. Configurer Dompdf
        $options = new Options();
        $options->set([
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'tempDir' => $this->getParameter('kernel.project_dir').'/var/tmp',
        ]);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 4. Retourner le PDF
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf(
                    'attachment; filename="receipt-%d-%s.pdf"',
                    $excursion->getExcursionId(),
                    date('Ymd-His')
                ),
            ]
        );
    }
}
