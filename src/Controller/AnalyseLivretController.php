<?php

namespace App\Controller;

use App\Entity\Livret;
use App\Entity\Depense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class AnalyseLivretController extends AbstractController
{
    #[Route('/lesLivrets/{id}/detail/analyse', name: 'analyse')]
    public function analyse(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $livret = $em->getRepository(Livret::class)->find($id);
        $utilisateur = $request->getSession()->get('utilisateur');

        if (!$utilisateur || $livret->getUtilisateur()->getId() !== $utilisateur['id']) {
            return $this->redirectToRoute('lesLivrets');
        }

        if (!$livret) {
            $this->addFlash('danger', 'Données invalides.');
            return $this->redirectToRoute('detailLivret', ['id' => $id]);
        }

        $donnees = $em->getRepository(Depense::class)->getSommeParCategorieParLivret($id);

        $libelles = [];
        $montants = [];

        foreach ($donnees as $d) {
            $libelles[] = $d['categorie'];
            $montants[] = $d['total'];
        }

        return $this->render('analyseLivret.html.twig', [
            'livret' => $livret,
            'libelles' => json_encode($libelles),
            'montants' => json_encode($montants),
        ]);
    }

}
